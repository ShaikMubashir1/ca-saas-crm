<?php

namespace App\Enums;

enum WhatsAppMessageStatus: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case READ = 'read';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED => 'Queued',
            self::SENT => 'Sent',
            self::DELIVERED => 'Delivered',
            self::READ => 'Read',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::QUEUED => 'bg-amber-50 text-amber-800',
            self::SENT => 'bg-blue-50 text-blue-800',
            self::DELIVERED => 'bg-emerald-50 text-emerald-800',
            self::READ => 'bg-teal-100 text-teal-900',
            self::FAILED => 'bg-red-50 text-[#ED1C24]',
        };
    }
}
