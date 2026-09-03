<?php

namespace App\Policies;

use App\Models\CommunicationMessage;
use App\Models\User;

class CommunicationMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, CommunicationMessage $message): bool
    {
        return $user->tenant_id === $message->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }
}

