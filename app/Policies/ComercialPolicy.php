<?php

namespace App\Policies;

use App\Models\Comercial;
use App\Models\User;

class ComercialPolicy
{
    /**
     * Solo el lider dueño o admin puede gestionar el comercial
     */
    public function update(User $user, Comercial $comercial): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isLider()) {
            return $user->lider->id === $comercial->lider_id;
        }

        return false;
    }

    public function delete(User $user, Comercial $comercial): bool
    {
        return $this->update($user, $comercial);
    }
}
