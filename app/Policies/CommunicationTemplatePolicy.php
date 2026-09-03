<?php

namespace App\Policies;

use App\Models\CommunicationTemplate;
use App\Models\User;

class CommunicationTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function view(User $user, CommunicationTemplate $template): bool
    {
        return $user->tenant_id === $template->tenant_id;
    }

    public function create(User $user): bool
    {
        return $user->tenant_id !== null;
    }

    public function update(User $user, CommunicationTemplate $template): bool
    {
        return $user->tenant_id === $template->tenant_id;
    }

    public function delete(User $user, CommunicationTemplate $template): bool
    {
        return $user->tenant_id === $template->tenant_id;
    }
}

