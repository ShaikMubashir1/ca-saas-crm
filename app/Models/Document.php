<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'service_id',
        'checklist_item_id',
        'name',
        'file_path',
        'category',
        'document_type',
        'original_filename',
        'mime_type',
        'file_size',
        'uploaded_by',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'replaced_by_id',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'status' => DocumentStatus::class,
            'verified_at' => 'datetime',
            'is_current' => 'boolean',
            'file_size' => 'integer',
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(DocumentChecklistItem::class, 'checklist_item_id');
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'replaced_by_id');
    }

    public function previousVersions(): HasMany
    {
        return $this->hasMany(Document::class, 'replaced_by_id');
    }
}
