<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComprarTicketRequest;
use App\Events\TicketComprado;
use App\Models\Combo;
use App\Models\Pago;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Services\HelppiuPayService;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function __construct(
        private ReferralService $referralService,
        private HelppiuPayService $helppiuPay,
    ) {}

    public function index(Request $request)
    {
        $tickets = $request->user()->tickets()
            ->with(['sorteo', 'pago'])
            ->latest()
            ->paginate(20);

        return view('cliente.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['sorteo', 'pago']);
        return view('cliente.tickets.show', compact('ticket'));
    }

    public function store(ComprarTicketRequest $request, Sorteo $sorteo): RedirectResponse
    {
        $comercialId = $this->referralService->resolve(
            $request->codigo_referido,
            $request->session()->get('referral_comercial_id'),
        );

        $combo = $request->filled('combo_id') ? Combo::findOrFail($request->combo_id) : null;
        $cantidad = $combo ? $combo->cantidad : (int) $request->cantidad;
        $monto = $combo ? $combo->precio : $sorteo->precio_ticket * $cantidad;

        try {
            $grupoCompra = Str::uuid()->toString();

            $tickets = DB::transaction(function () use ($request, $sorteo, $comercialId, $cantidad, $monto, $grupoCompra) {
                $disponibles = $sorteo->numerosDisponibles();
                if (count($disponibles) < $cantidad) {
                    throw new \RuntimeException('No hay suficientes números disponibles.');
                }

                // Selección verdaderamente aleatoria: barajar y tomar los primeros N
                shuffle($disponibles);
                $numerosElegidos = array_slice($disponibles, 0, $cantidad);
                $tickets = [];

                foreach ($numerosElegidos as $numero) {
                    $ticket = Ticket::create([
                        'sorteo_id' => $sorteo->id,
                        'user_id' => auth()->id(),
                        'comercial_id' => $comercialId,
                        'comprador_nombre' => $request->nombre,
                        'comprador_email' => $request->email,
                        'comprador_telefono' => $request->telefono,
                        'grupo_compra' => $grupoCompra,
                        'numero' => $numero,
                        'tipo_asignacion' => 'aleatorio',
                        'estado' => 'reservado',
                    ]);

                    Pago::create([
                        'ticket_id' => $ticket->id,
                        'metodo' => 'helppiu',
                        'monto' => $monto / $cantidad,
                        'estado' => 'pendiente',
                    ]);

                    $tickets[] = $ticket;
                }

                return $tickets;
            });

            // Create Helppiu Pay checkout session
            $reference = "TV-{$grupoCompra}";
            $session = $this->helppiuPay->createCheckoutSession([
                'reference' => $reference,
                'amount' => (int) $monto,
                'description' => "{$cantidad}x Ticket(s) — {$sorteo->nombre}",
                'success_url' => route('pago.exitoso', ['grupo' => $grupoCompra]),
                'cancel_url' => route('pago.cancelado', ['grupo' => $grupoCompra]),
                'customer_email' => $request->email,
                'customer_name' => $request->nombre,
                'idempotency_key' => $grupoCompra,
                'metadata' => [
                    'sorteo_id' => $sorteo->id,
                    'grupo_compra' => $grupoCompra,
                    'cantidad' => $cantidad,
                ],
            ]);

            // Save checkout session ID on all pagos
            Pago::whereIn('ticket_id', collect($tickets)->pluck('id'))
                ->update(['checkout_session_id' => $session['id']]);

            return redirect()->away($session['url']);

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Algunos números acaban de ser tomados. Intenta de nuevo.');
        }
    }

    public function historial(Request $request)
    {
        $sorteosConTickets = Sorteo::whereIn('estado', ['ejecutado', 'cerrado'])
            ->whereHas('tickets', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['tickets' => fn ($q) => $q->where('user_id', $request->user()->id)->with('pago')])
            ->latest('fecha_sorteo')
            ->paginate(10);

        return view('cliente.historial', compact('sorteosConTickets'));
    }
}
