<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comercial;
use App\Models\Pago;
use App\Models\Sorteo;
use App\Models\Ticket;

class ReporteController extends Controller
{
    public function index()
    {
        $sorteos = Sorteo::withCount([
            'tickets',
            'tickets as tickets_pagados_count' => fn ($q) => $q->where('estado', 'pagado'),
            'tickets as tickets_reservados_count' => fn ($q) => $q->where('estado', 'reservado'),
            'tickets as tickets_anulados_count' => fn ($q) => $q->where('estado', 'anulado'),
        ])->latest()->get();

        $ventasPorComercial = Comercial::with('user')
            ->withCount(['tickets as tickets_referidos_count' => fn ($q) => $q->where('estado', 'pagado')])
            ->get()
            ->map(function ($comercial) {
                $comercial->monto_recaudado = $comercial->tickets()
                    ->where('estado', 'pagado')
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                return $comercial;
            });

        $totalRecaudado = Pago::where('estado', 'verificado')->sum('monto');

        return view('admin.reportes.index', compact('sorteos', 'ventasPorComercial', 'totalRecaudado'));
    }
}
