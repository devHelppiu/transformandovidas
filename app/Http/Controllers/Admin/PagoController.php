<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VerificarPagoRequest;
use App\Events\PagoVerificado;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with(['ticket.user', 'ticket.sorteo', 'ticket.comercial'])
            ->latest()
            ->paginate(20);

        return view('admin.pagos.index', compact('pagos'));
    }

    public function show(Pago $pago)
    {
        $pago->load(['ticket.user', 'ticket.sorteo', 'ticket.comercial']);
        return view('admin.pagos.show', compact('pago'));
    }

    public function verificar(VerificarPagoRequest $request, Pago $pago): RedirectResponse
    {
        if ($pago->estado !== 'pendiente') {
            return back()->with('error', 'Este pago ya fue procesado.');
        }

        if ($request->accion === 'verificar') {
            $pago->update([
                'estado' => 'verificado',
                'verificado_por' => $request->user()->id,
                'verificado_at' => now(),
            ]);

            $pago->ticket->update(['estado' => 'pagado']);

            PagoVerificado::dispatch($pago);

            return back()->with('success', 'Pago verificado exitosamente.');
        }

        // Rechazar
        $pago->update([
            'estado' => 'rechazado',
            'verificado_por' => $request->user()->id,
            'verificado_at' => now(),
            'nota_rechazo' => $request->nota_rechazo,
        ]);

        $pago->ticket->update(['estado' => 'anulado']);

        return back()->with('success', 'Pago rechazado. El ticket ha sido anulado.');
    }
}
