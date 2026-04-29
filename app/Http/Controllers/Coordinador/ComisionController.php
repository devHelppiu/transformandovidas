<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use Illuminate\View\View;

class ComisionController extends Controller
{
    public function index(): View
    {
        $coordinador = auth()->user()->coordinador;

        $comisiones = Comision::deCoordinador($coordinador->id)
            ->with('sorteo')
            ->latest()
            ->paginate(20);

        $totales = [
            'acumulado' => Comision::deCoordinador($coordinador->id)->sum('monto_comision'),
            'pendiente' => Comision::deCoordinador($coordinador->id)->where('estado', 'pendiente')->sum('monto_comision'),
            'pagado' => Comision::deCoordinador($coordinador->id)->where('estado', 'pagada')->sum('monto_comision'),
        ];

        return view('coordinador.comisiones.index', compact('comisiones', 'totales'));
    }
}
