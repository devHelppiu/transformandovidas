<?php

namespace App\Services;

use App\Models\Comision;
use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComisionService
{
    /**
     * Calculate and create/update commissions for all comerciales
     * who have referred paid tickets in the given sorteo.
     */
    public function liquidar(Sorteo $sorteo): void
    {
        $ticketsPagados = $sorteo->tickets()
            ->where('estado', 'pagado')
            ->whereNotNull('comercial_id')
            ->get();

        $porComercial = $ticketsPagados->groupBy('comercial_id');

        DB::transaction(function () use ($porComercial, $sorteo) {
            foreach ($porComercial as $comercialId => $tickets) {
                $comercial = $tickets->first()->comercial;
                $totalTickets = $tickets->count();
                $montoRecaudado = $totalTickets * (float) $sorteo->precio_ticket;

                $montoComision = match ($comercial->comision_tipo) {
                    'porcentaje' => $montoRecaudado * ((float) $comercial->comision_valor / 100),
                    'fijo' => $totalTickets * (float) $comercial->comision_valor,
                    'meta' => 0, // Pendiente de definir reglas
                    default => 0,
                };

                if ($comercial->comision_tipo === 'meta') {
                    Log::warning("Comisión tipo 'meta' no implementada para comercial #{$comercialId}");
                }

                Comision::updateOrCreate(
                    [
                        'comercial_id' => $comercialId,
                        'sorteo_id' => $sorteo->id,
                    ],
                    [
                        'total_tickets_referidos' => $totalTickets,
                        'monto_recaudado' => $montoRecaudado,
                        'monto_comision' => $montoComision,
                        'estado' => 'pendiente',
                    ]
                );
            }
        });
    }
}
