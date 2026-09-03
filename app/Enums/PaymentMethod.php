<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case BANK_TRANSFER = 'bank_transfer';
    case UPI = 'upi';
    case CARD = 'card';
    case CHEQUE = 'cheque';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::BANK_TRANSFER => 'Bank Transfer (NEFT/RTGS/IMPS)',
            self::UPI => 'UPI',
            self::CARD => 'Credit/Debit Card',
            self::CHEQUE => 'Cheque',
            self::OTHER => 'Other',
        };
    }
}
