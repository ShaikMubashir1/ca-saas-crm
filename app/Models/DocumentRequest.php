<?php

namespace App\Models;

use App\Enums\DocumentRequestStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRequest extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'service_id',
        'financial_year_id',
        'created_by',
        'status',
        'message',
        'subject',
        'sent_at',
        'upload_token',
        'token_expires_at',
        'reminder_count',
        'last_reminder_sent_at',
        'max_reminders',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentRequestStatus::class,
            'sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'token_expires_at' => 'datetime',
            'last_reminder_sent_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocumentRequestItem::class);
    }
}
