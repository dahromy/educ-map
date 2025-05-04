<?php

namespace App\Policies;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EstablishmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any establishments.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Public access
    }

    /**
     * Determine whether the user can view the establishment.
     */
    public function view(?User $user, Establishment $establishment): bool
    {
        return true; // Public access
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Establishment $establishment): bool
    {
        // Admin can update any establishment
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        // Establishment user can update their associated establishment
        if ($user->hasRole('ROLE_ESTABLISHMENT')) {
            return $user->associated_establishment === $establishment->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Establishment $establishment): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Establishment $establishment): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Establishment $establishment): bool
    {
        return $user->hasRole('ROLE_ADMIN');
    }
}
