<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\User;

class PagoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Pago $pago): bool
    {
        return $user->isAdmin() || $pago->ticket->user_id === $user->id;
    }

    public function verificar(User $user, Pago $pago): bool
    {
        return $user->isAdmin() && $pago->estado === 'pendiente';
    }
}
