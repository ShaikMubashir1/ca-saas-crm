<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentChecklistItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'document_checklist_id',
        'name',
        'description',
        'is_required',
        'document_type',
        'sort_order',
        'status',
        'current_document_id',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
            'status' => DocumentStatus::class,
        ];
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(DocumentChecklist::class, 'document_checklist_id');
    }

    public function currentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'current_document_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'checklist_item_id')->latest();
    }
}
