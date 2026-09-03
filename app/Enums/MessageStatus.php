<?php

namespace App\Enums;

enum MessageStatus: string
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
            self::QUEUED => 'bg-slate-100 text-[#737373]',
            self::SENT => 'bg-blue-50 text-blue-800',
            self::DELIVERED => 'bg-purple-50 text-purple-800',
            self::READ => 'bg-emerald-50 text-emerald-800',
            self::FAILED => 'bg-red-50 text-[#ED1C24]',
        };
    }
}
