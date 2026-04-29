<?php

namespace App\Http\Controllers\Lider;

use App\Http\Controllers\Controller;
use App\Models\Sorteo;
use App\Models\Ticket;

class ReporteController extends Controller
{
    public function index()
    {
        $lider = auth()->user()->lider;
        $comercialesIds = $lider->comerciales()->pluck('id');

        // Estadísticas por comercial
        $comerciales = $lider->comerciales()
            ->with('user')
            ->get()
            ->map(function ($comercial) {
                $comercial->tickets_pagados = $comercial->tickets()->where('tickets.estado', 'pagado')->count();
                $comercial->monto_recaudado = $comercial->tickets()
                    ->where('tickets.estado', 'pagado')
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                return $comercial;
            });

        // Sorteos con métricas del equipo
        $sorteos = Sorteo::orderBy('fecha_sorteo', 'desc')
            ->take(6)
            ->get()
            ->map(function ($sorteo) use ($comercialesIds, $lider) {
                // Tickets del líder directo
                $sorteo->tickets_directos = Ticket::where('sorteo_id', $sorteo->id)
                    ->where('lider_id', $lider->id)
                    ->where('estado', 'pagado')
                    ->count();
                
                // Tickets vía comerciales
                $sorteo->tickets_comerciales = Ticket::where('sorteo_id', $sorteo->id)
                    ->whereIn('comercial_id', $comercialesIds)
                    ->where('estado', 'pagado')
                    ->count();
                
                $sorteo->tickets_equipo = $sorteo->tickets_directos + $sorteo->tickets_comerciales;
                $sorteo->monto_equipo = $sorteo->tickets_equipo * $sorteo->precio_ticket;
                
                return $sorteo;
            });

        // Totales
        $totales = [
            'tickets_directos' => $lider->ticketsDirectos()->where('estado', 'pagado')->count(),
            'tickets_comerciales' => Ticket::whereIn('comercial_id', $comercialesIds)->where('estado', 'pagado')->count(),
            'comision_acumulada' => $lider->comisiones()->sum('monto_comision'),
            'comision_pendiente' => $lider->comisiones()->where('estado', 'pendiente')->sum('monto_comision'),
            'comision_pagada' => $lider->comisiones()->where('estado', 'pagada')->sum('monto_comision'),
        ];
        $totales['tickets_totales'] = $totales['tickets_directos'] + $totales['tickets_comerciales'];

        // Comisión directa vs override
        $comisionDirecta = $lider->comisiones()->where('canal', 'directo')->sum('monto_comision');
        $comisionOverride = $lider->comisiones()->where('canal', 'override')->sum('monto_comision');
        $totales['comision_directa'] = $comisionDirecta;
        $totales['comision_override'] = $comisionOverride;

        return view('lider.reportes.index', compact('comerciales', 'sorteos', 'totales'));
    }
}
