<?php

namespace App\Listeners;

use App\Events\TicketComprado;
use App\Notifications\TicketReservadoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotificarCompradorListener implements ShouldQueue
{
    public function handle(TicketComprado $event): void
    {
        $ticket = $event->ticket;
        $notification = new TicketReservadoNotification($ticket);

        if ($ticket->user) {
            $ticket->user->notify($notification);
        } else {
            Notification::route('mail', $ticket->comprador_email)
                ->notify($notification);
        }
    }
}
