<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_request_id',
        'checklist_item_id',
        'item_name',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
        ];
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class, 'document_request_id');
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(DocumentChecklistItem::class, 'checklist_item_id');
    }
}
