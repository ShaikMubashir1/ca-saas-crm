<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'client_id', 'name', 'file_path', 'category', 'uploaded_by'];

    public function client() {
        return $this->belongsTo(Client::class);
    }
    
    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
