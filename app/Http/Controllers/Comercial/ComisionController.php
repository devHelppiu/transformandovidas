<?php

namespace App\Http\Controllers\Comercial;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $comercial = $request->user()->comercial;

        $comisiones = $comercial->comisiones()
            ->with('sorteo')
            ->latest()
            ->paginate(15);

        return view('comercial.comisiones.index', compact('comisiones', 'comercial'));
    }
}
