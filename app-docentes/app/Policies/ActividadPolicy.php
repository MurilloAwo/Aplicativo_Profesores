<?php

namespace App\Policies;

use App\Models\Actividad;
use App\Models\Grupo;
use App\Models\User;

class ActividadPolicy
{
    /**
     * El administrador puede visualizar cualquier actividad (bypass).
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
     * Un profesor solo puede ver sus propias actividades (grupo principal o multigrupo).
     */
    public function view(User $user, Actividad $actividad): bool
    {
        if ($user->id === $actividad->grupo?->profesor_id) {
            return true;
        }

        return $actividad->grupos()->where('profesor_id', $user->id)->exists();
    }

    /**
     * Un profesor solo puede registrar actividades en grupos propios y periodos que admitan registro.
     */
    public function create(User $user, ?Grupo $grupo = null): bool
    {
        if (! $grupo) {
            return $user->esProfesor();
        }

        return $user->id === $grupo->profesor_id
            && (bool) $grupo->periodoAcademico?->admiteRegistro();
    }

    /**
     * Un profesor solo edita sus actividades en periodos que admiten registro.
     */
    public function update(User $user, Actividad $actividad): bool
    {
        return $user->id === $actividad->grupo?->profesor_id
            && (bool) $actividad->grupo?->periodoAcademico?->admiteRegistro();
    }

    /**
     * Un profesor solo elimina sus actividades en periodos que admiten registro.
     */
    public function delete(User $user, Actividad $actividad): bool
    {
        return $user->id === $actividad->grupo?->profesor_id
            && (bool) $actividad->grupo?->periodoAcademico?->admiteRegistro();
    }
}
