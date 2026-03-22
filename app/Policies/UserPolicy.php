<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo usuarios');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        // Se puede editar a sí mismo o si es administrador
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasRole('ADMINISTRADOR') || $user->can('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        // No puede eliminarse a sí mismo
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasRole('ADMINISTRADOR');
    }
}
