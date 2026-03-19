<?php

namespace App\Http\Controllers;

use App\Events\TicketComprado;
use App\Models\Pago;
use App\Models\Ticket;
use App\Services\HelppiuPayService;
use Illuminate\Http\Request;

class PagoResultadoController extends Controller
{
    public function __construct(private HelppiuPayService $helppiuPay) {}

    public function exitoso(Request $request)
    {
        $grupo = $request->query('grupo');
        $tickets = Ticket::where('grupo_compra', $grupo)->with(['sorteo', 'pago'])->get();

        if ($tickets->isEmpty()) {
            return redirect('/');
        }

        // Fallback: if webhook hasn't updated yet, check Helppiu Pay API directly
        $primerPago = $tickets->first()?->pago;
        if ($primerPago && in_array($primerPago->estado, ['pendiente', 'procesando']) && $primerPago->checkout_session_id) {
            try {
                $session = $this->helppiuPay->getCheckoutSession($primerPago->checkout_session_id);
                $status = $session['status'] ?? '';

                if (in_array($status, ['approved', 'completed'])) {
                    $pagos = Pago::whereIn('ticket_id', $tickets->pluck('id'))
                        ->whereIn('estado', ['pendiente', 'procesando'])
                        ->get();
                    foreach ($pagos as $pago) {
                        $pago->update([
                            'estado' => 'verificado',
                            'metodo' => $session['payment_method'] ?? $session['transaction']['payment_method'] ?? 'helppiu',
                            'transaction_id' => $session['transaction_id'] ?? $session['transaction']['id'] ?? null,
                            'verificado_at' => now(),
                        ]);
                        $pago->ticket->update(['estado' => 'pagado']);
                        TicketComprado::dispatch($pago->ticket);
                    }
                    $tickets = Ticket::where('grupo_compra', $grupo)->with(['sorteo', 'pago'])->get();
                } elseif (in_array($status, ['declined', 'expired', 'error'])) {
                    $pagos = Pago::whereIn('ticket_id', $tickets->pluck('id'))
                        ->whereIn('estado', ['pendiente', 'procesando'])
                        ->get();
                    foreach ($pagos as $pago) {
                        $pago->update([
                            'estado' => 'rechazado',
                            'transaction_id' => $session['transaction_id'] ?? $session['transaction']['id'] ?? null,
                        ]);
                        $pago->ticket->update(['estado' => 'anulado']);
                    }
                    $tickets = Ticket::where('grupo_compra', $grupo)->with(['sorteo', 'pago'])->get();
                }
            } catch (\Throwable $e) {
                // API check failed — page will auto-refresh and webhook will update eventually
            }
        }

        $sorteo = $tickets->first()?->sorteo;
        $pago = $tickets->first()?->pago;
        $totalPagado = $tickets->sum(fn ($t) => $t->pago?->monto ?? 0);
        $estadoPago = $pago?->estado ?? 'pendiente';

        return view('pago.exitoso', compact('tickets', 'grupo', 'sorteo', 'totalPagado', 'estadoPago'));
    }

    public function cancelado(Request $request)
    {
        $grupo = $request->query('grupo');
        $tickets = Ticket::where('grupo_compra', $grupo)->with(['sorteo', 'pago'])->get();
        $sorteo = $tickets->first()?->sorteo;

        return view('pago.cancelado', compact('sorteo', 'grupo'));
    }
}
