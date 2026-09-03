<?php

namespace App\Enums;

enum ServiceStatus: string {
    case NOT_STARTED = 'not_started';
    case DOCS_PENDING = 'docs_pending';
    case IN_REVIEW = 'in_review';
    case READY_TO_FILE = 'ready_to_file';
    case FILED = 'filed';
    case ACKNOWLEDGED = 'acknowledged';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::NOT_STARTED => 'Not Started',
            self::DOCS_PENDING => 'Docs Pending',
            self::IN_REVIEW => 'In Review',
            self::READY_TO_FILE => 'Ready to File',
            self::FILED => 'Filed',
            self::ACKNOWLEDGED => 'Acknowledged',
            self::COMPLETED => 'Completed',
        };
    }
}
?>
