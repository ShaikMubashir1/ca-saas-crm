<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-slate-100 text-[#737373]',
            self::SENT => 'bg-blue-50 text-blue-800',
            self::PARTIALLY_PAID => 'bg-amber-50 text-amber-800',
            self::PAID => 'bg-emerald-50 text-emerald-800',
            self::OVERDUE => 'bg-red-50 text-[#ED1C24]',
            self::CANCELLED => 'bg-slate-200 text-slate-600',
        };
    }
}
