<?php
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model {
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'client_id', 'invoice_number', 'subtotal', 'tax_amount', 
        'total_amount', 'balance_due', 'issue_date', 'due_date', 'status', 'notes'
    ];

    protected function casts(): array {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function items() {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
