<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Auth\Access\Response;

class WhatsAppConversationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, WhatsAppConversation $whatsAppConversation): bool
    {
        return $user->tenant_id === $whatsAppConversation->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, WhatsAppConversation $whatsAppConversation): bool
    {
        return $user->tenant_id === $whatsAppConversation->tenant_id;
    }

    public function delete(User $user, WhatsAppConversation $whatsAppConversation): bool
    {
        return $user->tenant_id === $whatsAppConversation->tenant_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WhatsAppConversation $whatsAppConversation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WhatsAppConversation $whatsAppConversation): bool
    {
        return false;
    }
}
