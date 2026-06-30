<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'assigned_to', 'title', 'service_type', 
        'status', 'priority', 'due_date', 'description'
    ];

    protected function casts(): array {
        return [
            'due_date' => 'date',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function assignee() {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
