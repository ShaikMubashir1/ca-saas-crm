<?php

namespace App\Policies;

use App\Models\DocumentChecklist;
use App\Models\User;

class ChecklistPolicy
{
    /**
     * Any authenticated tenant user can view checklists.
     */
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    /**
     * A user can view a checklist only if it belongs to their tenant.
     */
    public function view(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }

    /**
     * Any authenticated tenant user can create checklists.
     */
    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    /**
     * A user can update a checklist in their own tenant.
     */
    public function update(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }

    /**
     * A user can manage (verify/reject items in) a checklist in their own tenant.
     */
    public function manage(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }

    /**
     * A user can delete a checklist in their own tenant.
     */
    public function delete(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }

    public function restore(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }

    public function forceDelete(User $user, DocumentChecklist $checklist): bool
    {
        return $user->tenant_id === $checklist->tenant_id;
    }
}

