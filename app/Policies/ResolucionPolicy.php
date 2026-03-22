<?php

namespace App\Policies;

use App\Models\Resolucion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ResolucionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo resoluciones');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo resoluciones');
    }

    public function update(User $user, Resolucion $resolucion): bool
    {
        // Solo administradores o creadores pueden editar resoluciones
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo resoluciones');
    }

    public function delete(User $user, Resolucion $resolucion): bool
    {
        // El administrador o el creador (si tiene el permiso) pueden eliminar.
        return $user->hasRole('ADMINISTRADOR');
    }
}
