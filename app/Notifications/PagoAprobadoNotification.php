<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PagoAprobadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Pago $pago)
    {
    }

    public function via(object $notifiable): array
    {
        return $notifiable instanceof \App\Models\User ? ['mail', 'database'] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->pago->ticket;
        $nombre = $ticket->comprador_nombre;

        return (new MailMessage)
            ->subject('Pago Verificado - Transformando Vidas')
            ->greeting("\u00a1Hola {$nombre}!")
            ->line("Tu pago para el ticket #{$ticket->numero} del sorteo \"{$ticket->sorteo->nombre}\" ha sido verificado.")
            ->line('Tu ticket est\u00e1 confirmado para participar en el sorteo.')
            ->action('Ver mi ticket', url(route('ticket.detalle', $ticket)))
            ->line('\u00a1Buena suerte!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'pago_id' => $this->pago->id,
            'ticket_numero' => $this->pago->ticket->numero,
            'mensaje' => "Pago verificado para ticket #{$this->pago->ticket->numero}.",
        ];
    }
}
