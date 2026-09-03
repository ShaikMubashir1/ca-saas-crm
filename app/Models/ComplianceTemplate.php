<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComplianceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'service_type',
        'frequency',
        'applicable_client_types',
        'description',
        'active',
        'default_due_day',
        'default_due_month',
        'quarter_rules',
    ];

    protected function casts(): array
    {
        return [
            'applicable_client_types' => 'array',
            'quarter_rules' => 'array',
            'active' => 'boolean',
            'default_due_day' => 'integer',
            'default_due_month' => 'integer',
        ];
    }

    public function instances(): HasMany
    {
        return $this->hasMany(ComplianceInstance::class);
    }
}
