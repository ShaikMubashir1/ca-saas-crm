<?php

namespace App\Enums;

enum TemplateCategory: string
{
    case UTILITY = 'utility';
    case MARKETING = 'marketing';
    case REMINDER = 'reminder';
    case AUTHENTICATION = 'authentication';

    public function label(): string
    {
        return match ($this) {
            self::UTILITY => 'Utility',
            self::MARKETING => 'Marketing',
            self::REMINDER => 'Reminder',
            self::AUTHENTICATION => 'Authentication',
        };
    }
}
