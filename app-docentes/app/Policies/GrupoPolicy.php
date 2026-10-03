<?php

namespace App\Policies;

use App\Models\Grupo;
use App\Models\User;

class GrupoPolicy
{
    /**
     * El administrador puede realizar cualquier acción sobre grupos.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Un profesor solo puede ver sus propios grupos.
     */
    public function view(User $user, Grupo $grupo): bool
    {
        return $user->id === $grupo->profesor_id;
    }

    /**
     * Solo el administrador crea grupos.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Solo el administrador edita grupos.
     */
    public function update(User $user, Grupo $grupo): bool
    {
        return false;
    }

    /**
     * Solo el administrador elimina grupos.
     */
    public function delete(User $user, Grupo $grupo): bool
    {
        return false;
    }
}
