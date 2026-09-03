<?php

namespace App\Enums;

enum DocumentRequestStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PARTIALLY_RECEIVED = 'partially_received';
    case COMPLETED = 'completed';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PARTIALLY_RECEIVED => 'Partially Received',
            self::COMPLETED => 'Completed',
            self::EXPIRED => 'Expired',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-[#737373]',
            self::SENT => 'bg-blue-50 text-blue-800',
            self::PARTIALLY_RECEIVED => 'bg-amber-50 text-amber-800',
            self::COMPLETED => 'bg-emerald-50 text-emerald-800',
            self::EXPIRED => 'bg-red-50 text-[#ED1C24]',
        };
    }
}
