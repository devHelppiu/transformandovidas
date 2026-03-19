<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketReservadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return $notifiable instanceof \App\Models\User ? ['mail', 'database'] : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nombre = $this->ticket->comprador_nombre;
        return (new MailMessage)
            ->subject('Ticket Reservado - Transformando Vidas')
            ->greeting("\u00a1Hola {$nombre}!")
            ->line("Tu ticket #{$this->ticket->numero} ha sido reservado para el sorteo \"{$this->ticket->sorteo->nombre}\".")
            ->line("Monto a pagar: $" . number_format($this->ticket->sorteo->precio_ticket, 0, ',', '.'))
            ->action('Ver mi ticket', url(route('ticket.detalle', $this->ticket)))
            ->line('Recuerda subir tu comprobante de pago para confirmar la reserva.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'numero' => $this->ticket->numero,
            'sorteo' => $this->ticket->sorteo->nombre,
            'mensaje' => "Ticket #{$this->ticket->numero} reservado.",
        ];
    }
}
