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

        // Verify HMAC-SHA256 signature if webhook secret is configured
        $webhookSecret = config('services.helppiu.webhook_secret');
        if ($webhookSecret && ! HelppiuPayService::verifyWebhookSignature($payload, $signature)) {
            Log::warning('Webhook Helppiu: firma inválida', ['signature' => $signature]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->all();
        $event = $data['event'] ?? '';
        $tx = $data['data'] ?? [];

        Log::info('Webhook Helppiu recibido', ['event' => $event, 'reference' => $tx['reference'] ?? '']);

        match ($event) {
            'transaction.approved' => $this->handleApproved($tx),
            'transaction.pending' => $this->handlePending($tx),
            'transaction.declined', 'transaction.failed', 'transaction.error' => $this->handleDeclined($tx),
            'transaction.reversed' => $this->handleReversed($tx),
            'checkout.completed' => $this->handleCheckoutCompleted($tx),
            default => Log::info('Webhook Helppiu: evento no manejado', ['event' => $event]),
        };

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

        Log::warning('Webhook Helppiu: transacción reversada', [
            'reference' => $tx['reference'] ?? '',
            'grupo_compra' => $grupoCompra,
        ]);
    }

    private function handleCheckoutCompleted(array $tx): void
    {
        // Checkout completed - the transaction result comes via transaction.approved/declined
        Log::info('Webhook Helppiu: checkout completado', ['reference' => $tx['reference'] ?? '']);
    }
}
