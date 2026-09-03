<?php

namespace App\Enums;

enum ComplianceStatus: string
{
    case UPCOMING = 'upcoming';
    case DOCS_PENDING = 'docs_pending';
    case IN_PREPARATION = 'in_preparation';
    case UNDER_REVIEW = 'under_review';
    case READY_TO_FILE = 'ready_to_file';
    case FILED = 'filed';
    case ACKNOWLEDGED = 'acknowledged';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::UPCOMING => 'Upcoming',
            self::DOCS_PENDING => 'Docs Pending',
            self::IN_PREPARATION => 'In Preparation',
            self::UNDER_REVIEW => 'Under Review',
            self::READY_TO_FILE => 'Ready to File',
            self::FILED => 'Filed',
            self::ACKNOWLEDGED => 'Acknowledged',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::UPCOMING => 'bg-slate-100 text-slate-700',
            self::DOCS_PENDING => 'bg-amber-50 text-amber-800',
            self::IN_PREPARATION => 'bg-blue-50 text-blue-800',
            self::UNDER_REVIEW => 'bg-purple-50 text-purple-800',
            self::READY_TO_FILE => 'bg-teal-50 text-teal-800',
            self::FILED => 'bg-emerald-50 text-emerald-800',
            self::ACKNOWLEDGED => 'bg-green-100 text-green-900',
            self::OVERDUE => 'bg-red-50 text-[#ED1C24]',
            self::CANCELLED => 'bg-slate-200 text-slate-600',
        };
    }

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::UPCOMING => [self::DOCS_PENDING, self::IN_PREPARATION, self::CANCELLED],
            self::DOCS_PENDING => [self::IN_PREPARATION, self::CANCELLED],
            self::IN_PREPARATION => [self::UNDER_REVIEW, self::READY_TO_FILE, self::DOCS_PENDING, self::CANCELLED],
            self::UNDER_REVIEW => [self::READY_TO_FILE, self::IN_PREPARATION, self::CANCELLED],
            self::READY_TO_FILE => [self::FILED, self::CANCELLED],
            self::FILED => [self::ACKNOWLEDGED],
            self::ACKNOWLEDGED => [],
            self::OVERDUE => [self::DOCS_PENDING, self::IN_PREPARATION, self::FILED, self::CANCELLED],
            self::CANCELLED => [self::UPCOMING],
        };
    }
}
