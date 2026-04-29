<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use Illuminate\View\View;

class ComisionController extends Controller
{
    public function index(): View
    {
        $lider = auth()->user()->lider;

        $comisiones = Comision::deLider($lider->id)
            ->with('sorteo')
            ->latest()
            ->paginate(20);

        $totales = [
            'acumulado' => Comision::deLider($lider->id)->sum('monto_comision'),
            'pendiente' => Comision::deLider($lider->id)->where('estado', 'pendiente')->sum('monto_comision'),
            'pagado' => Comision::deLider($lider->id)->where('estado', 'pagada')->sum('monto_comision'),
        ];

        return view('lider.comisiones.index', compact('comisiones', 'totales'));
    }
}
