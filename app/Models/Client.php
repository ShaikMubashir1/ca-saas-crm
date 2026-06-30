<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'entity_type', 'name', 'email', 'phone', 
        'pan', 'aadhaar', 'gstin', 'tan', 'cin', 'address'
    ];

    protected function casts(): array {
        return [
            'pan' => 'encrypted',
            'aadhaar' => 'encrypted',
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

    public function notes() {
        return $this->morphMany(Note::class, 'notable');
    }
}
