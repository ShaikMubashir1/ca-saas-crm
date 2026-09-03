<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'compliance_instance_id', 'financial_year_id', 'assigned_to', 'reviewer_id',
        'title', 'service_type', 'status', 'priority', 'due_date', 'description', 'completed_at', 'created_by'
    ];

    protected function casts(): array {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Determine if the task is overdue.
     * A task is overdue if due_date has passed and it is not completed.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'completed';
    }

    /**
     * Get the effective display status (auto-detects overdue).
     */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->is_overdue) {
            return 'overdue';
        }
        return $this->status;
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function complianceInstance() {
        return $this->belongsTo(ComplianceInstance::class);
    }

    public function financialYear() {
        return $this->belongsTo(FinancialYear::class);
    }

    public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewer() {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}
