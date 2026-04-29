<?php

namespace App\Http\Controllers\Coordinador;

use App\Http\Controllers\Controller;
use App\Models\Comercial;
use App\Models\Lider;
use App\Models\Sorteo;
use App\Models\Ticket;

class ReporteController extends Controller
{
    public function index()
    {
        $coordinador = auth()->user()->coordinador;
        $lideresIds = $coordinador->lideres()->pluck('id');
        $comercialesIds = Comercial::whereIn('lider_id', $lideresIds)->pluck('id');

        // Ventas por líder (de mi rama)
        $lideres = Lider::where('coordinador_id', $coordinador->id)
            ->with('user')
            ->get()
            ->map(function ($lider) {
                $lider->tickets_directos = $lider->ticketsDirectos()->where('estado', 'pagado')->count();
                $lider->tickets_comerciales = Ticket::whereIn('comercial_id', $lider->comerciales()->pluck('id'))
                    ->where('estado', 'pagado')
                    ->count();
                $lider->total_tickets = $lider->tickets_directos + $lider->tickets_comerciales;
                
                // Calcular recaudo
                $preciosDirect = $lider->ticketsDirectos()
                    ->where('tickets.estado', 'pagado')
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                $preciosComerciales = Ticket::whereIn('comercial_id', $lider->comerciales()->pluck('id'))
                    ->where('tickets.estado', 'pagado')
                    ->join('sorteos', 'tickets.sorteo_id', '=', 'sorteos.id')
                    ->sum('sorteos.precio_ticket');
                $lider->monto_recaudado = $preciosDirect + $preciosComerciales;
                
                return $lider;
            });

        // Ventas por sorteo (de mi rama)
        $sorteos = Sorteo::orderBy('fecha_sorteo', 'desc')
            ->take(6)
            ->get()
            ->map(function ($sorteo) use ($comercialesIds, $lideresIds) {
                $sorteo->tickets_rama = Ticket::where('sorteo_id', $sorteo->id)
                    ->where('estado', 'pagado')
                    ->where(function ($q) use ($comercialesIds, $lideresIds) {
                        $q->whereIn('comercial_id', $comercialesIds)
                          ->orWhereIn('lider_id', $lideresIds);
                    })
                    ->count();
                $sorteo->monto_rama = $sorteo->tickets_rama * $sorteo->precio_ticket;
                return $sorteo;
            });

        // Totales
        $totales = [
            'tickets_rama' => Ticket::where('estado', 'pagado')
                ->where(function ($q) use ($comercialesIds, $lideresIds) {
                    $q->whereIn('comercial_id', $comercialesIds)
                      ->orWhereIn('lider_id', $lideresIds);
                })
                ->count(),
            'comision_acumulada' => $coordinador->comisiones()->sum('monto_comision'),
            'comision_pendiente' => $coordinador->comisiones()->where('estado', 'pendiente')->sum('monto_comision'),
            'comision_pagada' => $coordinador->comisiones()->where('estado', 'pagada')->sum('monto_comision'),
        ];

        return view('coordinador.reportes.index', compact('lideres', 'sorteos', 'totales'));
    }
}
