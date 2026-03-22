<?php

namespace App\Policies;

use App\Models\LegalEntity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LegalEntityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo personas juridicas') || $user->can('legal-entities.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('legal-entities.create');
    }

    public function update(User $user, LegalEntity $legalEntity): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('legal-entities.edit');
    }

    public function delete(User $user, LegalEntity $legalEntity): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('legal-entities.delete');
    }
}
