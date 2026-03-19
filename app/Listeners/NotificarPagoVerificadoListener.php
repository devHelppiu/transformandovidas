<?php

namespace App\Listeners;

use App\Events\PagoVerificado;
use App\Notifications\PagoAprobadoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarPagoVerificadoListener implements ShouldQueue
{
    public function handle(PagoVerificado $event): void
    {
        $pago = $event->pago;
        $ticket = $pago->ticket;
        $notification = new PagoAprobadoNotification($pago);

        if ($ticket->user) {
            $ticket->user->notify($notification);
        } else {
            Notification::route('mail', $ticket->comprador_email)
                ->notify($notification);
        }
    }
}
