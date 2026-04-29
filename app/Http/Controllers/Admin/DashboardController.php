<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\Coordinador;
use App\Models\Lider;
use App\Models\Pago;
use App\Models\Sorteo;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sorteos' => Sorteo::count(),
            'sorteos_activos' => Sorteo::where('estado', 'activo')->count(),
            'total_tickets' => Ticket::count(),
            'tickets_pagados' => Ticket::where('estado', 'pagado')->count(),
            'pagos_pendientes' => Pago::where('estado', 'pendiente')->count(),
            'total_clientes' => User::where('role', 'cliente')->count(),
            'total_comerciales' => User::where('role', 'comercial')->count(),
            'total_coordinadores' => Coordinador::where('is_active', true)->count(),
            'total_lideres' => Lider::where('is_active', true)->count(),
            'comisiones_pendientes' => Comision::where('estado', 'pendiente')->sum('monto_comision'),
            'comisiones_acumuladas' => Comision::sum('monto_comision'),
        ];

        $sorteoActivo = Sorteo::where('estado', 'activo')->latest()->first();
        $ultimosPagos = Pago::with('ticket.user', 'ticket.sorteo')
            ->where('estado', 'pendiente')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'sorteoActivo', 'ultimosPagos'));
    }
}
