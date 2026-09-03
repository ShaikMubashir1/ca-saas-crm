<?php

namespace App\Policies;

use App\Models\Communication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, Communication $communication): bool
    {
        return $user->tenant_id === $communication->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, Communication $communication): bool
    {
        return $user->tenant_id === $communication->tenant_id;
    }

    public function delete(User $user, Communication $communication): bool
    {
        return $user->tenant_id === $communication->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Communication $communication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Communication $communication): bool
    {
        return false;
    }
}
