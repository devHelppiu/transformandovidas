<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isAdmin() || $ticket->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isCliente();
    }

    public function subirComprobante(User $user, Ticket $ticket): bool
    {
        return $ticket->user_id === $user->id && $ticket->estado === 'reservado';
    }
}
