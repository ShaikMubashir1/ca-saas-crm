<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-800',
            self::COMPLETED => 'bg-emerald-50 text-emerald-800',
            self::FAILED => 'bg-red-50 text-[#ED1C24]',
            self::REFUNDED => 'bg-purple-50 text-purple-800',
        };
    }
}
