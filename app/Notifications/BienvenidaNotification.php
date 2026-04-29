<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BienvenidaNotification extends Notification
{
    use Queueable;

    protected string $password;
    protected string $role;

    public function __construct(string $password, string $role = 'usuario')
    {
        $this->password = $password;
        $this->role = $role;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $roleName = match($this->role) {
            'admin' => 'Administrador',
            'coordinador' => 'Coordinador',
            'lider' => 'Líder',
            'comercial' => 'Comercial',
            default => 'Usuario',
        };

        $loginUrl = url('/login');

        return (new MailMessage)
            ->subject('¡Bienvenido a Transformando Vidas!')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line("Tu cuenta de **{$roleName}** ha sido creada exitosamente en Transformando Vidas.")
            ->line('Aquí están tus credenciales de acceso:')
            ->line("**Email:** {$notifiable->email}")
            ->line("**Contraseña:** {$this->password}")
            ->action('Iniciar Sesión', $loginUrl)
            ->line('Te recomendamos cambiar tu contraseña después de iniciar sesión.')
            ->salutation('¡Bienvenido al equipo!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'role' => $this->role,
        ];
    }
}
