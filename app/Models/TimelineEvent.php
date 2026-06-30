<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'client_id', 'user_id', 'event_type', 'description', 'properties'];

    protected function casts(): array {
        return [
            'properties' => 'array',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
