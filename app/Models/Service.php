<?php

namespace App\Models;

use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'financial_year_id',
        'type',
        'status',
        'assigned_staff_id',
        'reviewer_id',
        'start_date',
        'deadline',
        'filing_date',
        'arn',
        'notes',
    ];

    protected $casts = [
        'type' => ServiceType::class,
        'status' => ServiceStatus::class,
        'start_date' => 'date',
        'deadline' => 'date',
        'filing_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function checklist()
    {
        return $this->hasOne(DocumentChecklist::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function complianceInstances()
    {
        return $this->hasMany(ComplianceInstance::class);
    }
}
