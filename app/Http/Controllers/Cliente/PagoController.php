<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubirComprobanteRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PagoController extends Controller
{
    public function subirComprobante(SubirComprobanteRequest $request, Ticket $ticket): RedirectResponse
    {
        $pago = $ticket->pago;

        if (! $pago || $pago->estado !== 'pendiente') {
            return back()->with('error', 'No se puede subir comprobante para este ticket.');
        }

        // Verify ticket ownership via session email
        $consultaEmail = $request->session()->get('consulta_email');
        if (!$consultaEmail || strtolower($ticket->comprador_email) !== $consultaEmail) {
            return back()->with('error', 'No tienes permiso para subir comprobante a este ticket.');
        }

        // Store in private storage (not publicly accessible)
        $path = $request->file('comprobante')->store(
            'comprobantes/' . date('Y/m'),
            'local'
        );

        $pago->update([
            'comprobante_url' => $path,
            'referencia_pago' => $request->referencia_pago,
        ]);

        return redirect()->route('ticket.detalle', $ticket)
            ->with('success', 'Comprobante subido exitosamente. El administrador verificará tu pago.');
    }

    /**
     * Serve comprobante file securely (admin only)
     */
    public function verComprobante(Ticket $ticket)
    {
        $pago = $ticket->pago;

        if (!$pago || !$pago->comprobante_url) {
            abort(404, 'Comprobante no encontrado.');
        }

        if (!auth()->user()?->isAdmin()) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        $path = storage_path('app/' . $pago->comprobante_url);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return response()->file($path);
    }
}
