<?php

namespace App\Models;

use App\Enums\WhatsAppMessageDirection;
use App\Enums\WhatsAppMessageStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'tenant_id',
        'conversation_id',
        'client_id',
        'direction',
        'message_type',
        'provider_message_id',
        'template_name',
        'body',
        'media_url',
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
            'direction' => WhatsAppMessageDirection::class,
            'status' => WhatsAppMessageStatus::class,
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'failed_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(WhatsAppConversation::class, 'conversation_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
