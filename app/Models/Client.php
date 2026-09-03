<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ClientType;

class Client extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'entity_type',
        'client_type',
        'name',
        'email',
        'phone',
        'pan',
        'aadhaar',
        'gstin',
        'tan',
        'cin',
        'address',
    ];

    protected function casts(): array {
        return [
            'pan' => 'encrypted',
            'aadhaar' => 'encrypted',
            'client_type' => ClientType::class,
        ];
    }

    public function credentials() {
        return $this->hasMany(ClientCredential::class);
    }

    public function dscDetails() {
        return $this->hasMany(DscDetail::class);
    }

    public function timelineEvents() {
        return $this->hasMany(TimelineEvent::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }

    public function notes() {
        return $this->morphMany(Note::class, 'notable');
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }

    public function services() {
        return $this->hasMany(Service::class);
    }

    public function communications() {
        return $this->hasMany(Communication::class);
    }

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function complianceInstances() {
        return $this->hasMany(ComplianceInstance::class);
    }

    public function whatsappConversations() {
        return $this->hasMany(WhatsAppConversation::class);
    }

    public function whatsappMessages() {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function whatsappConsent() {
        return $this->hasOne(WhatsAppConsent::class);
    }
}
