<?php

namespace App\Http\Controllers;

use App\Events\TicketComprado;
use App\Models\Pago;
use App\Models\Ticket;
use App\Services\HelppiuPayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HelppiuWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Helppiu-Signature', '');

        // Verify HMAC-SHA256 signature — REQUIRED in production
        $webhookSecret = config('services.helppiu.webhook_secret');
        
        if (empty($webhookSecret)) {
            Log::channel('helppiu')->critical('HELPPIU_WEBHOOK_SECRET no configurado, rechazando webhook');
            return response()->json(['error' => 'Webhook secret not configured'], 503);
        }
        
        // FIX 53: Include timestamp for replay attack protection
        $timestamp = $request->header('X-Helppiu-Timestamp');
        if (empty($signature) || ! HelppiuPayService::verifyWebhookSignature($payload, $signature, $timestamp)) {
            Log::channel('helppiu')->warning('Firma inválida', [
                'signature_present' => !empty($signature),
                'timestamp_present' => !empty($timestamp),
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        $event = $data['event'] ?? '';
        $tx = $data['data'] ?? [];

        Log::channel('helppiu')->info('Webhook recibido', ['event' => $event, 'reference' => $tx['reference'] ?? '']);

        // FIX 54: Idempotency check - prevent duplicate processing
        $txId = $tx['transaction_id'] ?? null;
        $webhookEvent = null;
        if ($txId) {
            $existing = \App\Models\WebhookEvent::where('transaction_id', $txId)->first();
            if ($existing && $existing->processed_at) {
                Log::channel('helppiu')->info('Webhook duplicado, ignorando', [
                    'transaction_id' => $txId,
                    'event' => $event,
                ]);
                return response()->json(['status' => 'already_processed']);
            }
            
            $webhookEvent = $existing ?: \App\Models\WebhookEvent::create([
                'source' => 'helppiu',
                'event_type' => $event,
                'transaction_id' => $txId,
                'payload' => $data,
            ]);
        }

        match ($event) {
            'transaction.approved' => $this->handleApproved($tx),
            'transaction.pending' => $this->handlePending($tx),
            'transaction.declined', 'transaction.failed', 'transaction.error' => $this->handleDeclined($tx),
            'transaction.reversed' => $this->handleReversed($tx),
            'checkout.completed' => $this->handleCheckoutCompleted($tx),
            default => Log::channel('helppiu')->info('Evento no manejado', ['event' => $event]),
        };

        // Mark webhook as processed
        if ($webhookEvent) {
            $webhookEvent->update(['processed_at' => now()]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function resolveGrupoCompra(array $tx): string
    {
        $reference = $tx['reference'] ?? '';
        return str_replace('TV-', '', $reference);
    }

    private function getPagosPendientes(string $grupoCompra)
    {
        return Pago::whereHas('ticket', fn ($q) => $q->where('grupo_compra', $grupoCompra))
            ->where('estado', 'pendiente')
            ->get();
    }

    private function handleApproved(array $tx): void
    {
        $grupoCompra = $this->resolveGrupoCompra($tx);

        $pagos = Pago::whereHas('ticket', fn ($q) => $q->where('grupo_compra', $grupoCompra))
            ->whereIn('estado', ['pendiente', 'procesando'])
            ->get();

        foreach ($pagos as $pago) {
            $pago->update([
                'estado' => 'verificado',
                'metodo' => $tx['payment_method'] ?? 'helppiu',
                'transaction_id' => $tx['transaction_id'] ?? null,
                'referencia_pago' => $tx['reference'] ?? null,
                'verificado_at' => now(),
            ]);

            $pago->ticket->update(['estado' => 'pagado']);
            TicketComprado::dispatch($pago->ticket);
        }
    }

    private function handlePending(array $tx): void
    {
        $grupoCompra = $this->resolveGrupoCompra($tx);
        $pagos = $this->getPagosPendientes($grupoCompra);

        foreach ($pagos as $pago) {
            $pago->update([
                'estado' => 'procesando',
                'metodo' => $tx['payment_method'] ?? 'helppiu',
                'transaction_id' => $tx['transaction_id'] ?? null,
            ]);
        }
    }

    private function handleDeclined(array $tx): void
    {
        $grupoCompra = $this->resolveGrupoCompra($tx);

        $pagos = Pago::whereHas('ticket', fn ($q) => $q->where('grupo_compra', $grupoCompra))
            ->whereIn('estado', ['pendiente', 'procesando'])
            ->get();

        foreach ($pagos as $pago) {
            $pago->update([
                'estado' => 'rechazado',
                'transaction_id' => $tx['transaction_id'] ?? null,
            ]);

            $pago->ticket->update(['estado' => 'anulado']);
        }
    }

    private function handleReversed(array $tx): void
    {
        $grupoCompra = $this->resolveGrupoCompra($tx);

        $pagos = Pago::whereHas('ticket', fn ($q) => $q->where('grupo_compra', $grupoCompra))
            ->where('estado', 'verificado')
            ->get();

        foreach ($pagos as $pago) {
            $pago->update([
                'estado' => 'reversado',
                'transaction_id' => $tx['transaction_id'] ?? null,
            ]);

            $pago->ticket->update(['estado' => 'anulado']);
        }

        Log::channel('helppiu')->warning('Transacción reversada', [
            'reference' => $tx['reference'] ?? '',
            'grupo_compra' => $grupoCompra,
        ]);
    }

    private function handleCheckoutCompleted(array $tx): void
    {
        // Checkout completed - the transaction result comes via transaction.approved/declined
        Log::channel('helppiu')->info('Checkout completado', ['reference' => $tx['reference'] ?? '']);
    }
}
