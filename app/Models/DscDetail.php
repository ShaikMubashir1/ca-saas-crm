<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DscDetail extends Model {
    use HasFactory;

    protected $fillable = ['client_id', 'holder_name', 'expiry_date', 'password', 'is_with_firm'];

    protected function casts(): array {
        return [
            'password' => 'encrypted',
            'expiry_date' => 'date',
            'is_with_firm' => 'boolean',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
