<?php

namespace App\Http\Controllers\Comercial;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $comercial = $request->user()->comercial;

        $stats = [
            'total_referidos' => $comercial->tickets()->where('estado', 'pagado')->count(),
            'total_clientes' => $comercial->tickets()->where('estado', 'pagado')
                ->distinct('user_id')->count('user_id'),
            'monto_recaudado' => $comercial->tickets()
                ->where('tickets.estado', 'pagado')
                ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                ->sum('sorteos.precio_ticket'),
        ];

        $ticketsPorSorteo = $comercial->tickets()
            ->with('sorteo', 'user')
            ->where('estado', 'pagado')
            ->latest()
            ->take(20)
            ->get()
            ->groupBy('sorteo_id');

        // Enlace de referido universal (redirige al sorteo activo)
        $enlace = route('referido', $comercial->codigo_ref);
        
        // Sorteos activos para enlaces específicos
        $sorteosActivos = \App\Models\Sorteo::where('estado', 'activo')->latest()->get();

        return view('comercial.dashboard', compact('comercial', 'stats', 'ticketsPorSorteo', 'enlace', 'sorteosActivos'));
    }
}
