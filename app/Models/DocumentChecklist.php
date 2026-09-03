<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentChecklist extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'service_id',
        'service_type',
        'title',
        'description',
        'is_template',
    ];

    protected function casts(): array
    {
        return [
            'is_template' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocumentChecklistItem::class, 'document_checklist_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Calculate completion metrics.
     */
    public function getMetricsAttribute(): array
    {
        $items = $this->items;
        $totalRequired = $items->where('is_required', true)->count();
        $verifiedRequired = $items->where('is_required', true)->where('status', 'verified')->count();
        $receivedCount = $items->where('status', 'received')->count();
        $pendingCount = $items->where('status', 'pending')->count();
        $rejectedCount = $items->where('status', 'rejected')->count();

        $percentage = $totalRequired > 0 ? (int) round(($verifiedRequired / $totalRequired) * 100) : 0;

        return [
            'total_items' => $items->count(),
            'total_required' => $totalRequired,
            'verified_required' => $verifiedRequired,
            'received' => $receivedCount,
            'pending' => $pendingCount,
            'rejected' => $rejectedCount,
            'percentage' => $percentage,
        ];
    }
}
