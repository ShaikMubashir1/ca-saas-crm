<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConsent extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'whatsapp_consents';

    protected $fillable = [
        'tenant_id',
        'client_id',
        'phone_number',
        'marketing_opt_in',
        'transactional_opt_in',
        'opted_in_at',
        'opted_out_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'marketing_opt_in' => 'boolean',
            'transactional_opt_in' => 'boolean',
            'opted_in_at' => 'datetime',
            'opted_out_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
