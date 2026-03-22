<?php

namespace App\Policies;

use App\Models\NaturalPerson;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NaturalPersonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo personas naturales') || $user->can('natural-people.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('natural-people.create');
    }

    public function update(User $user, NaturalPerson $naturalPerson): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('natural-people.edit');
    }

    public function delete(User $user, NaturalPerson $naturalPerson): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('natural-people.delete');
    }
}
