<?php

namespace App\Models;

use App\Enums\ComplianceStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComplianceInstance extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'service_id',
        'financial_year_id',
        'compliance_template_id',
        'period',
        'due_date',
        'status',
        'assigned_to',
        'reviewer_id',
        'filing_date',
        'acknowledgement_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ComplianceStatus::class,
            'due_date' => 'date',
            'filing_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function financialYear(): BelongsTo
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ComplianceTemplate::class, 'compliance_template_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class);
    }
}
