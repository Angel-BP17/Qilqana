<?php

namespace App\Policies;

use App\Models\Charge;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChargePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo cargos');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Charge $charge): bool
    {
        // Un usuario puede ver el cargo si es administrador, si tiene permiso general
        // o si el cargo le pertenece (fue el creador o es el asignado).
        if ($user->hasRole('ADMINISTRADOR') || $user->can('modulo cargos')) {
            return true;
        }

        return $charge->user_id === $user->id || ($charge->signature && $charge->signature->assigned_to === $user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ADMINISTRADOR') || $user->can('modulo cargos');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Charge $charge): bool
    {
        // Solo el creador del cargo puede editarlo, siempre que no esté firmado o rechazado.
        if ($charge->user_id !== $user->id && ! $user->hasRole('ADMINISTRADOR')) {
            return false;
        }

        $status = $charge->signature?->signature_status;

        return in_array($status, ['pendiente'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Charge $charge): bool
    {
        // Solo el administrador o el dueño del cargo pueden eliminarlo,
        // siempre que no tenga procesos de firma finalizados.
        if (! $user->hasRole('ADMINISTRADOR') && $charge->user_id !== $user->id) {
            return false;
        }

        $status = $charge->signature?->signature_status;

        return $status === 'pendiente';
    }

    /**
     * Determine whether the user can sign the model.
     */
    public function sign(User $user, Charge $charge): bool
    {
        if ($user->hasRole('ADMINISTRADOR')) {
            return true;
        }

        // El asignado puede firmar si el estado es pendiente.
        return $charge->signature &&
               $charge->signature->assigned_to === $user->id &&
               $charge->signature->signature_status === 'pendiente';
    }
}
