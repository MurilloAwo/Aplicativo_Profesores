<?php

namespace App\Policies;

use App\Models\Actividad;
use App\Models\Evidencia;
use App\Models\User;

class EvidenciaPolicy
{
    /**
     * El administrador puede visualizar y descargar cualquier evidencia.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->esAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Puede ver o descargar la evidencia si tiene acceso de lectura a la actividad padre.
     */
    public function view(User $user, Evidencia $evidencia): bool
    {
        if (! $evidencia->actividad) {
            return false;
        }

        return $user->can('view', $evidencia->actividad);
    }

    /**
     * Puede subir evidencias si puede modificar la actividad padre (periodo activo).
     */
    public function create(User $user, Actividad $actividad): bool
    {
        return $user->can('update', $actividad);
    }

    /**
     * Puede eliminar la evidencia si puede modificar la actividad padre (periodo activo).
     */
    public function delete(User $user, Evidencia $evidencia): bool
    {
        if (! $evidencia->actividad) {
            return false;
        }

        return $user->can('update', $evidencia->actividad);
    }
}
