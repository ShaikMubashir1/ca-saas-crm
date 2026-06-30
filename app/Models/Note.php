<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'notable_type', 'notable_id', 'user_id', 'content'];

    public function notable() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
