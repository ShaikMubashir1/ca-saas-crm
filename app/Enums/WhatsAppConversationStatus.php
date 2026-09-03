<?php

namespace App\Enums;

enum WhatsAppConversationStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Open',
            self::CLOSED => 'Closed',
            self::ARCHIVED => 'Archived',
        };
    }
}
