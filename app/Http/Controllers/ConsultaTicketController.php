<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class ConsultaTicketController extends Controller
{
    public function index()
    {
        return view('consulta.index');
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $tickets = Ticket::where('comprador_email', $request->email)
            ->with(['sorteo', 'pago'])
            ->latest()
            ->get();

        // Store email in session for verification on ticket detail
        $request->session()->put('consulta_email', strtolower($request->email));

        return view('consulta.resultados', [
            'tickets' => $tickets,
            'email' => $request->email,
        ]);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $ticket->load(['sorteo', 'pago']);

        // Check if user already verified ownership via session
        $consultaEmail = $request->session()->get('consulta_email');
        if ($consultaEmail && strtolower($ticket->comprador_email) === $consultaEmail) {
            return view('consulta.ticket', compact('ticket'));
        }

        // If accessing directly (e.g., from email link), show verification form
        return view('consulta.verificar', compact('ticket'));
    }

    public function verificar(Request $request, Ticket $ticket)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $emailIngresado = strtolower($request->email);
        
        if ($emailIngresado !== strtolower($ticket->comprador_email)) {
            return back()->withErrors(['email' => 'El email no coincide con el registrado para este ticket.']);
        }

        // Store verified email in session
        $request->session()->put('consulta_email', $emailIngresado);

        return redirect()->route('ticket.detalle', $ticket);
    }
}
