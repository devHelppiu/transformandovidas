<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use Illuminate\Console\Command;

class LiberarTicketsExpirados extends Command
{
    protected $signature = 'tickets:liberar-expirados';

    protected $description = 'Anula tickets reservados hace más de 30 minutos sin comprobante de pago';

    public function handle(): int
    {
        $expirados = Ticket::where('estado', 'reservado')
            ->where('created_at', '<', now()->subMinutes(30))
            ->whereHas('pago', fn ($q) => $q->where('estado', 'pendiente')->whereNull('comprobante_url'))
            ->get();

        $count = $expirados->count();

        foreach ($expirados as $ticket) {
            $ticket->update(['estado' => 'anulado']);
            $ticket->pago?->update(['estado' => 'rechazado', 'nota_rechazo' => 'Expirado: sin comprobante en 30 minutos.']);
        }

        $this->info("Se liberaron {$count} tickets expirados.");

        return self::SUCCESS;
    }
}
