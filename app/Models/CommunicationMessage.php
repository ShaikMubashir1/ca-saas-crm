<?php

namespace App\Models;

use App\Enums\CommunicationChannel;
use App\Enums\MessageDirection;
use App\Enums\MessageStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'channel',
        'direction',
        'message_type',
        'template_id',
        'provider_message_id',
        'recipient',
        'subject',
        'body',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'channel' => CommunicationChannel::class,
            'direction' => MessageDirection::class,
            'status' => MessageStatus::class,
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'failed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CommunicationTemplate::class, 'template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class);
    }
}
