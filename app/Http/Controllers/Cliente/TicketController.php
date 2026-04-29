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
        $userId = $request->user()->id;
        $search = trim((string) $request->input('q', ''));

        // Una fila por sorteo (cada card del prototipo = un sorteo)
        $sorteoIdsQuery = Ticket::query()
            ->where('user_id', $userId)
            ->select('sorteo_id')
            ->selectRaw('MAX(created_at) as ultima_compra_at')
            ->groupBy('sorteo_id')
            ->orderByDesc('ultima_compra_at');

        if ($search !== '') {
            $sorteoIdsQuery->whereHas('sorteo', fn ($q) =>
                $q->where('nombre', 'like', "%{$search}%")
            );
        }

        $sorteosPaginados = $sorteoIdsQuery->paginate(10)->withQueryString();

        // Cargar todos los tickets del usuario para esos sorteos
        $ticketsPorSorteo = Ticket::query()
            ->where('user_id', $userId)
            ->whereIn('sorteo_id', $sorteosPaginados->pluck('sorteo_id'))
            ->with(['sorteo', 'pago'])
            ->orderBy('numero')
            ->get()
            ->groupBy('sorteo_id');

        // Construir DTOs por sorteo, preservando el orden del paginador
        $compras = $sorteosPaginados->getCollection()->map(function ($row) use ($ticketsPorSorteo) {
            $tickets = $ticketsPorSorteo->get($row->sorteo_id) ?? collect();
            $first = $tickets->first();
            if (! $first) {
                return null;
            }

            $totalMonto = $tickets->sum(fn ($t) => (float) (optional($t->pago)->monto ?? 0));

            // Estado consolidado:
            //   - "rechazado" si al menos un pago fue rechazado
            //   - "verificado" sólo si TODOS los pagos están verificados
            //   - "pendiente" en cualquier otro caso
            $estados = $tickets->pluck('pago.estado')->filter();
            $estadoPago = match (true) {
                $estados->contains('rechazado')                                  => 'rechazado',
                $estados->isNotEmpty() && $estados->every(fn ($e) => $e === 'verificado') => 'verificado',
                default                                                          => 'pendiente',
            };

            return (object) [
                'sorteo'          => $first->sorteo,
                'fecha_compra'    => $tickets->max('created_at'), // compra más reciente
                'cantidad'        => $tickets->count(),
                'precio_unitario' => $first->sorteo->precio_ticket,
                'total'           => $totalMonto,
                'estado_pago'     => $estadoPago,
                'tickets'         => $tickets, // colección completa para los pills (numero + link)
            ];
        })->filter()->values();

        // Reemplazar la colección del paginador por los DTOs (mantiene total/links)
        $sorteosPaginados->setCollection($compras);

        return view('cliente.tickets.index', [
            'compras' => $sorteosPaginados,
            'search'  => $search,
        ]);
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

        // Capturar lider_id si la venta viene de un lider (no de un comercial)
        $liderId = null;
        if (!$comercialId) {
            $liderId = $request->session()->get('referral_lider_id');
        }

        $combo = $request->filled('combo_id') ? Combo::findOrFail($request->combo_id) : null;
        $cantidad = $combo ? $combo->cantidad : (int) $request->cantidad;
        $monto = $combo ? $combo->precio : $sorteo->precio_ticket * $cantidad;

        // Determinar si el usuario eligió números manualmente
        $numerosElegidos = $request->filled('numeros') && is_array($request->numeros) 
            ? $request->numeros 
            : null;
        $tipoAsignacion = $numerosElegidos ? 'manual' : 'aleatorio';

        try {
            $grupoCompra = Str::uuid()->toString();

            $tickets = DB::transaction(function () use ($request, $sorteo, $comercialId, $liderId, $cantidad, $monto, $grupoCompra, $numerosElegidos, $tipoAsignacion) {
                
                // Si el usuario eligió números manualmente, usar lock optimista
                if ($numerosElegidos) {
                    // Bloquear filas existentes si las hay (lock optimista)
                    $ocupados = Ticket::where('sorteo_id', $sorteo->id)
                        ->whereIn('numero', $numerosElegidos)
                        ->whereIn('estado', ['reservado', 'pagado'])
                        ->lockForUpdate()
                        ->pluck('numero')
                        ->toArray();

                    if (count($ocupados) > 0) {
                        throw new \RuntimeException(
                            'Los números ' . implode(', ', $ocupados) . ' fueron tomados. Selecciona otros.'
                        );
                    }
                } else {
                    // Selección aleatoria
                    $disponibles = $sorteo->numerosDisponibles();
                    if (count($disponibles) < $cantidad) {
                        throw new \RuntimeException('No hay suficientes números disponibles.');
                    }

                    shuffle($disponibles);
                    $numerosElegidos = array_slice($disponibles, 0, $cantidad);
                }

                $tickets = [];
                foreach ($numerosElegidos as $numero) {
                    $ticket = Ticket::create([
                        'sorteo_id' => $sorteo->id,
                        'user_id' => auth()->id(),
                        'comercial_id' => $comercialId,
                        'lider_id' => $liderId,
                        'comprador_nombre' => $request->nombre,
                        'comprador_email' => $request->email,
                        'comprador_telefono' => $request->telefono,
                        'grupo_compra' => $grupoCompra,
                        'numero' => $numero,
                        'tipo_asignacion' => $tipoAsignacion,
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

            // MODO DEMO: Si el sorteo tiene pago simulado, aprobar automáticamente
            // NOTA: Deshabilitado en producción por seguridad (FIX 51)
            if ($sorteo->pago_simulado && !app()->environment('production')) {
                // Simular delay de "procesamiento"
                sleep(2);
                
                // Marcar pagos como verificados
                Pago::whereIn('ticket_id', collect($tickets)->pluck('id'))
                    ->update([
                        'estado' => 'verificado',
                        'metodo' => 'simulado',
                        'transaction_id' => 'DEMO-' . strtoupper(uniqid()),
                        'referencia_pago' => $reference,
                        'verificado_at' => now(),
                    ]);
                
                // Marcar tickets como pagados
                Ticket::whereIn('id', collect($tickets)->pluck('id'))
                    ->update(['estado' => 'pagado']);
                
                // Disparar eventos
                foreach ($tickets as $ticket) {
                    $ticket->refresh();
                    TicketComprado::dispatch($ticket);
                }
                
                // Guardar en sesión para la página de éxito
                $request->session()->put('consulta_email', strtolower($request->email));
                
                return redirect()->route('pago.exitoso', ['grupo' => $grupoCompra])
                    ->with('success', '¡Pago procesado exitosamente!');
            }

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
