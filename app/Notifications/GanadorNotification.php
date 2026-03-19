<?php

namespace App\Notifications;

use App\Models\Sorteo;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GanadorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Sorteo $sorteo, private Ticket $ticket)
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
            ->subject('\u00a1FELICITACIONES! Eres el ganador - Transformando Vidas')
            ->greeting("\u00a1\u00a1\u00a1Felicitaciones {$nombre}!!!")
            ->line("Has ganado el sorteo \"{$this->sorteo->nombre}\" con el n\u00famero #{$this->ticket->numero}.")
            ->line("Premio: $" . number_format($this->sorteo->valor_premio, 0, ',', '.'))
            ->action('Ver detalles', url(route('ticket.detalle', $this->ticket)))
            ->line('Nos pondremos en contacto contigo para coordinar la entrega del premio.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'sorteo_id' => $this->sorteo->id,
            'sorteo_nombre' => $this->sorteo->nombre,
            'numero_ganador' => $this->ticket->numero,
            'premio' => $this->sorteo->valor_premio,
            'mensaje' => "\u00a1Ganaste el sorteo {$this->sorteo->nombre}!",
        ];
    }
}
