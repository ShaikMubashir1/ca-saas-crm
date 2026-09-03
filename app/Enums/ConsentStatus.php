<?php

namespace App\Enums;

enum ConsentStatus: string
{
    case OPTED_IN = 'opted_in';
    case OPTED_OUT = 'opted_out';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::OPTED_IN => 'Opted In',
            self::OPTED_OUT => 'Opted Out',
            self::PENDING => 'Pending',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::OPTED_IN => 'bg-emerald-50 text-emerald-800',
            self::OPTED_OUT => 'bg-red-50 text-[#ED1C24]',
            self::PENDING => 'bg-amber-50 text-amber-800',
        };
    }
}
