<?php

namespace App\Http\Controllers;

use App\Models\Sorteo;

class SorteoPublicoController extends Controller
{
    public function show(Sorteo $sorteo)
    {
        $ticketsVendidos = $sorteo->ticketsVendidos();
        $combos = $sorteo->combos()->where('activo', true)->orderBy('cantidad')->get();

        return view('sorteo.show', compact('sorteo', 'ticketsVendidos', 'combos'));
    }
}
