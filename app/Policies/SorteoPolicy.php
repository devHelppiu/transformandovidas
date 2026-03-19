<?php

namespace App\Policies;

use App\Models\Sorteo;
use App\Models\User;

class SorteoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Sorteo $sorteo): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Sorteo $sorteo): bool
    {
        return $user->isAdmin() && $sorteo->estado === 'borrador';
    }

    public function delete(User $user, Sorteo $sorteo): bool
    {
        return $user->isAdmin() && $sorteo->estado === 'borrador';
    }

    public function ejecutar(User $user, Sorteo $sorteo): bool
    {
        return $user->isAdmin() && $sorteo->estado === 'cerrado';
    }

    public function activar(User $user, Sorteo $sorteo): bool
    {
        return $user->isAdmin() && $sorteo->estado === 'borrador';
    }

    public function cerrar(User $user, Sorteo $sorteo): bool
    {
        return $user->isAdmin() && $sorteo->estado === 'activo';
    }
}
