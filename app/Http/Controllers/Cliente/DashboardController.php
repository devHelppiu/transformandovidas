<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Sorteo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $misTickets = $user->tickets()
            ->with(['sorteo', 'pago'])
            ->latest()
            ->take(10)
            ->get();

        $sorteoActivo = Sorteo::where('estado', 'activo')->latest()->first();

        return view('cliente.dashboard', compact('misTickets', 'sorteoActivo'));
    }
}
