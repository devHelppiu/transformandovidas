<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\Sorteo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $lider = auth()->user()->lider;

        // Estadísticas del equipo
        $totalComerciales = $lider->comerciales()->count();
        $comercialesActivos = $lider->comercialesActivos()->count();

        // Comisiones del lider
        $comisionesAcumuladas = Comision::deLider($lider->id)->sum('monto_comision');
        $comisionesPendientes = Comision::deLider($lider->id)->where('estado', 'pendiente')->sum('monto_comision');
        $comisionesPagadas = Comision::deLider($lider->id)->where('estado', 'pagada')->sum('monto_comision');

        // Comerciales con sus stats
        $comerciales = $lider->comerciales()
            ->with('user')
            ->get()
            ->map(function ($comercial) {
                $comercial->tickets_vendidos = $comercial->tickets()->pagado()->count();
                $comercial->monto_recaudado = $comercial->tickets()
                    ->pagado()
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                return $comercial;
            });

        // Sorteo activo
        $sorteoActivo = Sorteo::where('estado', 'activo')->first();

        // URL de referido del lider
        $urlReferido = $lider->urlReferido();

        return view('lider.dashboard', compact(
            'lider',
            'totalComerciales',
            'comercialesActivos',
            'comisionesAcumuladas',
            'comisionesPendientes',
            'comisionesPagadas',
            'comerciales',
            'sorteoActivo',
            'urlReferido'
        ));
    }
}
