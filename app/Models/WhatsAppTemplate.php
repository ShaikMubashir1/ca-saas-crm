<?php

namespace App\Models;

use App\Enums\WhatsAppTemplateCategory;
use App\Enums\WhatsAppTemplateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppTemplate extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'language',
        'body',
        'variables',
        'provider_template_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'category' => WhatsAppTemplateCategory::class,
            'status' => WhatsAppTemplateStatus::class,
            'variables' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
