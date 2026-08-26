<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCredential extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'client_id', 'portal_name', 'username', 'password', 'notes'];

    protected function casts(): array {
        return [
            'password' => 'encrypted',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
