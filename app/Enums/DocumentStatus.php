<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case PENDING = 'pending';
    case RECEIVED = 'received';
    case UNDER_REVIEW = 'under_review';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RECEIVED => 'Received',
            self::UNDER_REVIEW => 'Under Review',
            self::VERIFIED => 'Verified',
            self::REJECTED => 'Rejected',
        };
    }
}
