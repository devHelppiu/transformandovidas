<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\Comercial;
use App\Models\Sorteo;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $coordinador = auth()->user()->coordinador;

        // Estadísticas de la rama
        $totalLideres = $coordinador->lideres()->count();
        $lideresActivos = $coordinador->lideresActivos()->count();
        $totalComerciales = $coordinador->totalComercialesActivos();

        // Comisiones del coordinador
        $comisionesAcumuladas = Comision::deCoordinador($coordinador->id)->sum('monto_comision');
        $comisionesPendientes = Comision::deCoordinador($coordinador->id)->where('estado', 'pendiente')->sum('monto_comision');
        $comisionesPagadas = Comision::deCoordinador($coordinador->id)->where('estado', 'pagada')->sum('monto_comision');

        // Lideres con sus stats
        $lideres = $coordinador->lideres()
            ->with(['user', 'comerciales'])
            ->withCount('comerciales')
            ->get()
            ->map(function ($lider) {
                $lider->total_recaudado = $lider->comerciales->sum(function ($comercial) {
                    return $comercial->tickets()->pagado()->sum('sorteo_id'); // TODO: sumar precio real
                });
                return $lider;
            });

        // Sorteo activo
        $sorteoActivo = Sorteo::where('estado', 'activo')->first();

        return view('coordinador.dashboard', compact(
            'coordinador',
            'totalLideres',
            'lideresActivos',
            'totalComerciales',
            'comisionesAcumuladas',
            'comisionesPendientes',
            'comisionesPagadas',
            'lideres',
            'sorteoActivo'
        ));
    }
}
