<?php

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\ConsentStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationConsent extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'channel',
        'purpose',
        'status',
        'source',
        'consented_at',
        'revoked_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'channel' => CommunicationChannel::class,
            'status' => ConsentStatus::class,
            'consented_at' => 'datetime',
            'revoked_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
