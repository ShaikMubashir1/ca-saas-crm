<?php

namespace App\Models;

use App\Enums\WhatsAppConversationStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'tenant_id',
        'client_id',
        'phone_number',
        'wa_contact_id',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => WhatsAppConversationStatus::class,
            'last_message_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }
}
