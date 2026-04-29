<?php

namespace App\Services;

use App\Models\Comision;
use App\Models\ComisionConfig;
use App\Models\Comercial;
use App\Models\Coordinador;
use App\Models\Lider;
use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComisionService
{
    /**
     * Calcula las comisiones proyectadas para un comercial en sorteos activos.
     */
    public function proyectadas(Comercial $comercial): Collection
    {
        $sorteosActivos = Sorteo::where('estado', 'activo')->get();
        
        return $sorteosActivos->map(function ($sorteo) use ($comercial) {
            $ticketsPagados = $sorteo->tickets()
                ->where('comercial_id', $comercial->id)
                ->where('estado', 'pagado')
                ->count();
            
            if ($ticketsPagados === 0) {
                return null;
            }
            
            $montoRecaudado = $ticketsPagados * (float) $sorteo->precio_ticket;
            
            // Usar comision_configs
            $config = ComisionConfig::obtenerConfig('comercial', 'directo', $sorteo->id);
            $montoComision = $config ? $config->calcularComision($montoRecaudado) : 0;
            
            return (object) [
                'sorteo_id' => $sorteo->id,
                'sorteo' => $sorteo,
                'total_tickets_referidos' => $ticketsPagados,
                'monto_recaudado' => $montoRecaudado,
                'monto_comision' => $montoComision,
                'estado' => 'proyectada',
                'es_proyectada' => true,
            ];
        })->filter();
    }

    /**
     * Calcula las comisiones proyectadas para un lider en sorteos activos.
     */
    public function proyectadasLider(Lider $lider): Collection
    {
        $sorteosActivos = Sorteo::where('estado', 'activo')->get();
        
        return $sorteosActivos->map(function ($sorteo) use ($lider) {
            // Tickets directos del lider
            $ticketsDirectos = $sorteo->tickets()
                ->where('lider_id', $lider->id)
                ->where('estado', 'pagado')
                ->count();
            
            // Tickets de sus comerciales (override)
            $ticketsOverride = $sorteo->tickets()
                ->whereIn('comercial_id', $lider->comerciales()->pluck('id'))
                ->where('estado', 'pagado')
                ->count();
            
            if ($ticketsDirectos === 0 && $ticketsOverride === 0) {
                return null;
            }
            
            $precioTicket = (float) $sorteo->precio_ticket;
            $montoDirecto = $ticketsDirectos * $precioTicket;
            $montoOverride = $ticketsOverride * $precioTicket;
            
            $configDirecto = ComisionConfig::obtenerConfig('lider', 'directo', $sorteo->id);
            $configOverride = ComisionConfig::obtenerConfig('lider', 'override', $sorteo->id);
            
            $comisionDirecta = $configDirecto ? $configDirecto->calcularComision($montoDirecto) : 0;
            $comisionOverride = $configOverride ? $configOverride->calcularComision($montoOverride) : 0;
            
            return (object) [
                'sorteo_id' => $sorteo->id,
                'sorteo' => $sorteo,
                'total_tickets_referidos' => $ticketsDirectos + $ticketsOverride,
                'monto_recaudado' => $montoDirecto + $montoOverride,
                'monto_comision' => $comisionDirecta + $comisionOverride,
                'estado' => 'proyectada',
                'es_proyectada' => true,
            ];
        })->filter();
    }

    /**
     * Calcula y crea/actualiza comisiones en cascada para un sorteo.
     * Comercial → Lider (override) → Coordinador (override)
     */
    public function liquidar(Sorteo $sorteo): void
    {
        $ticketsPagados = $sorteo->tickets()
            ->where('estado', 'pagado')
            ->get();

        DB::transaction(function () use ($ticketsPagados, $sorteo) {
            $precioTicket = (float) $sorteo->precio_ticket;
            
            // Agrupar por comercial (ventas de comerciales)
            $porComercial = $ticketsPagados->whereNotNull('comercial_id')->groupBy('comercial_id');
            
            foreach ($porComercial as $comercialId => $tickets) {
                $comercial = Comercial::find($comercialId);
                if (!$comercial) continue;
                
                $totalTickets = $tickets->count();
                $montoRecaudado = $totalTickets * $precioTicket;
                
                // 1. Comisión del Comercial (directo)
                $this->acumularComision(
                    'Comercial', $comercialId, $sorteo->id,
                    $totalTickets, $montoRecaudado, 'comercial', 'directo'
                );
                
                // 2. Comisión del Lider (override) si el comercial tiene lider
                if ($comercial->lider_id) {
                    $this->acumularComision(
                        'Lider', $comercial->lider_id, $sorteo->id,
                        $totalTickets, $montoRecaudado, 'lider', 'override'
                    );
                    
                    // 3. Comisión del Coordinador (override) si el lider tiene coordinador
                    $lider = Lider::find($comercial->lider_id);
                    if ($lider && $lider->coordinador_id) {
                        $this->acumularComision(
                            'Coordinador', $lider->coordinador_id, $sorteo->id,
                            $totalTickets, $montoRecaudado, 'coordinador', 'override'
                        );
                    }
                }
            }
            
            // Agrupar por lider (ventas directas de lideres)
            $porLider = $ticketsPagados->whereNotNull('lider_id')->whereNull('comercial_id')->groupBy('lider_id');
            
            foreach ($porLider as $liderId => $tickets) {
                $lider = Lider::find($liderId);
                if (!$lider) continue;
                
                $totalTickets = $tickets->count();
                $montoRecaudado = $totalTickets * $precioTicket;
                
                // 1. Comisión del Lider (directo)
                $this->acumularComision(
                    'Lider', $liderId, $sorteo->id,
                    $totalTickets, $montoRecaudado, 'lider', 'directo'
                );
                
                // 2. Comisión del Coordinador (override)
                if ($lider->coordinador_id) {
                    $this->acumularComision(
                        'Coordinador', $lider->coordinador_id, $sorteo->id,
                        $totalTickets, $montoRecaudado, 'coordinador', 'override'
                    );
                }
            }
        });
    }

    /**
     * Acumula comisión para un destinatario (crea o actualiza)
     */
    private function acumularComision(
        string $recipientType,
        int $recipientId,
        int $sorteoId,
        int $totalTickets,
        float $montoRecaudado,
        string $rol,
        string $canal
    ): void {
        $config = ComisionConfig::obtenerConfig($rol, $canal, $sorteoId);
        
        if (!$config) {
            Log::warning("No hay configuración de comisión para {$rol}/{$canal} en sorteo #{$sorteoId}");
            return;
        }
        
        $montoComision = $config->calcularComision($montoRecaudado);
        
        if ($montoComision <= 0) {
            return;
        }
        
        // Buscar comisión existente o crear nueva
        $comision = Comision::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('sorteo_id', $sorteoId)
            ->first();
        
        if ($comision) {
            // Sumar a la comisión existente
            $comision->update([
                'total_tickets_referidos' => $comision->total_tickets_referidos + $totalTickets,
                'monto_recaudado' => $comision->monto_recaudado + $montoRecaudado,
                'monto_comision' => $comision->monto_comision + $montoComision,
                'porcentaje_aplicado' => $config->valor,
            ]);
        } else {
            Comision::create([
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'sorteo_id' => $sorteoId,
                'total_tickets_referidos' => $totalTickets,
                'monto_recaudado' => $montoRecaudado,
                'monto_comision' => $montoComision,
                'porcentaje_aplicado' => $config->valor,
                'estado' => 'pendiente',
            ]);
        }
    }
}
