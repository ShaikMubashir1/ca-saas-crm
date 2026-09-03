<?php

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\TemplateCategory;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'category',
        'channel',
        'template_key',
        'provider_template_id',
        'language',
        'body',
        'variables',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'category' => TemplateCategory::class,
            'channel' => CommunicationChannel::class,
            'variables' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CommunicationMessage::class, 'template_id');
    }
}
