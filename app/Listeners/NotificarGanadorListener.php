<?php

namespace App\Listeners;

use App\Events\SorteoEjecutado;
use App\Notifications\GanadorNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarGanadorListener implements ShouldQueue
{
    public function handle(SorteoEjecutado $event): void
    {
        $sorteo = $event->sorteo;

        $ticketGanador = $sorteo->tickets()
            ->where('numero', $sorteo->numero_ganador)
            ->where('estado', 'pagado')
            ->first();

        if ($ticketGanador) {
            $notification = new GanadorNotification($sorteo, $ticketGanador);

            if ($ticketGanador->user) {
                $ticketGanador->user->notify($notification);
            } else {
                Notification::route('mail', $ticketGanador->comprador_email)
                    ->notify($notification);
            }
        }
    }
}
