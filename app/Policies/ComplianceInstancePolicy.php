<?php

namespace App\Policies;

use App\Models\ComplianceInstance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ComplianceInstancePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, ComplianceInstance $complianceInstance): bool
    {
        return $user->tenant_id === $complianceInstance->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, ComplianceInstance $complianceInstance): bool
    {
        return $user->tenant_id === $complianceInstance->tenant_id;
    }

    public function delete(User $user, ComplianceInstance $complianceInstance): bool
    {
        return $user->tenant_id === $complianceInstance->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ComplianceInstance $complianceInstance): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ComplianceInstance $complianceInstance): bool
    {
        return false;
    }
}
