<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comercial;
use App\Models\Comision;
use App\Models\Coordinador;
use App\Models\Lider;
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

        // Comerciales con stats
        $ventasPorComercial = Comercial::with('user', 'lider.user')
            ->whereHas('user')
            ->withCount(['tickets as tickets_referidos_count' => fn ($q) => $q->where('estado', 'pagado')])
            ->get()
            ->map(function ($comercial) {
                $comercial->monto_recaudado = $comercial->tickets()
                    ->where('tickets.estado', 'pagado')
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                $comercial->comision_total = Comision::where('recipient_type', 'Comercial')
                    ->where('recipient_id', $comercial->id)
                    ->sum('monto_comision');
                return $comercial;
            })
            ->sortByDesc('tickets_referidos_count');

        // Líderes con stats
        $ventasPorLider = Lider::with('user', 'coordinador.user')
            ->whereHas('user')
            ->withCount('comerciales')
            ->get()
            ->map(function ($lider) {
                // Tickets directos del líder
                $lider->tickets_directos = Ticket::where('lider_id', $lider->id)
                    ->where('estado', 'pagado')
                    ->count();
                // Tickets de sus comerciales
                $comercialIds = $lider->comerciales->pluck('id');
                $lider->tickets_comerciales = Ticket::whereIn('comercial_id', $comercialIds)
                    ->where('estado', 'pagado')
                    ->count();
                $lider->total_tickets = $lider->tickets_directos + $lider->tickets_comerciales;
                $lider->comision_total = Comision::where('recipient_type', 'Lider')
                    ->where('recipient_id', $lider->id)
                    ->sum('monto_comision');
                return $lider;
            })
            ->sortByDesc('total_tickets');

        // Coordinadores con stats
        $ventasPorCoordinador = Coordinador::with('user')
            ->whereHas('user')
            ->withCount('lideres')
            ->get()
            ->map(function ($coord) {
                // Total de comerciales en su rama
                $coord->total_comerciales = Comercial::whereHas('lider', fn ($q) => 
                    $q->where('coordinador_id', $coord->id)
                )->count();
                // Tickets de toda su rama
                $liderIds = $coord->lideres->pluck('id');
                $comercialIds = Comercial::whereIn('lider_id', $liderIds)->pluck('id');
                $coord->tickets_rama = Ticket::where(function ($q) use ($liderIds, $comercialIds) {
                    $q->whereIn('lider_id', $liderIds)
                      ->orWhereIn('comercial_id', $comercialIds);
                })->where('estado', 'pagado')->count();
                $coord->comision_total = Comision::where('recipient_type', 'Coordinador')
                    ->where('recipient_id', $coord->id)
                    ->sum('monto_comision');
                return $coord;
            })
            ->sortByDesc('tickets_rama');

        $totalRecaudado = Pago::where('estado', 'verificado')->sum('monto');

        // Datos para charts
        $chartSorteos = $sorteos->take(6)->map(fn ($s) => [
            'nombre' => $s->nombre,
            'pagados' => $s->tickets_pagados_count,
            'reservados' => $s->tickets_reservados_count,
        ]);

        return view('admin.reportes.index', compact(
            'sorteos', 
            'ventasPorComercial', 
            'ventasPorLider',
            'ventasPorCoordinador',
            'totalRecaudado',
            'chartSorteos'
        ));
    }
}
