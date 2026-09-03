<?php

namespace App\Enums;

enum WhatsAppTemplateCategory: string
{
    case UTILITY = 'utility';
    case MARKETING = 'marketing';
    case AUTHENTICATION = 'authentication';

    public function label(): string
    {
        return match ($this) {
            self::UTILITY => 'Utility / Transactional',
            self::MARKETING => 'Marketing / Broadcast',
            self::AUTHENTICATION => 'Authentication / OTP',
        };
    }
}
