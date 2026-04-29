<?php

namespace App\Http\Controllers\Comercial;

use App\Http\Controllers\Controller;
use App\Services\ComisionService;
use Illuminate\Http\Request;

class ComisionController extends Controller
{
    public function __construct(
        private ComisionService $comisionService
    ) {}

    public function index(Request $request)
    {
        $comercial = $request->user()->comercial;

        // Comisiones liquidadas (sorteos finalizados)
        $comisiones = $comercial->comisiones()
            ->with('sorteo')
            ->latest()
            ->paginate(15);

        // Comisiones proyectadas (sorteos activos)
        $proyectadas = $this->comisionService->proyectadas($comercial);

        return view('comercial.comisiones.index', compact('comisiones', 'comercial', 'proyectadas'));
    }
}
