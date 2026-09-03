<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * All authenticated users of same tenant can view any document listing.
     */
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    /**
     * A user can view a document only if they belong to the same tenant.
     */
    public function view(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    /**
     * Any authenticated tenant user can upload/create documents.
     */
    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    /**
     * A user can update a document in their own tenant.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    /**
     * A user can download a document only from their own tenant.
     */
    public function download(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    /**
     * A user can verify a document in their own tenant.
     */
    public function verify(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    /**
     * A user can reject a document in their own tenant.
     */
    public function reject(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    /**
     * A user can delete a document only from their own tenant.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    public function restore(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return $user->tenant_id === $document->tenant_id;
    }
}

