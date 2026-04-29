<?php

namespace App\Policies;

use App\Models\Lider;
use App\Models\User;

class LiderPolicy
{
    /**
     * Solo el coordinador dueño puede gestionar el lider
     */
    public function update(User $user, Lider $lider): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCoordinador()) {
            return $user->coordinador->id === $lider->coordinador_id;
        }

        return false;
    }

    public function delete(User $user, Lider $lider): bool
    {
        return $this->update($user, $lider);
    }
}
