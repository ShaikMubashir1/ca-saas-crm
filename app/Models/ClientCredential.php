<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCredential extends Model {
    use HasFactory;

    protected $fillable = ['client_id', 'portal_name', 'username', 'password', 'notes'];

    protected function casts(): array {
        return [
            'password' => 'encrypted',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
