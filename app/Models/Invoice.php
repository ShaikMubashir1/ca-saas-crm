<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'client_id',
        'invoice_number',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'balance_due',
        'issue_date',
        'due_date',
        'status',
        'notes',
        'terms',
    ];

    protected function casts(): array
    {
        return [
            'status' => InvoiceStatus::class,
            'issue_date' => 'date',
            'due_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'balance_due' => 'decimal:2',
        ];
    }

    public static function generateNextInvoiceNumber(int $tenantId): string
    {
        $year = date('Y');
        $prefix = "INV-{$year}-";

        $lastNumber = static::where('tenant_id', $tenantId)
            ->where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $seq = (int) end($parts) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function recalculateTotals(): void
    {
        $hasItems = $this->items()->count() > 0;
        
        if ($hasItems) {
            $subtotal = (float)$this->items()->sum('amount');
            $tax = (float)$this->items()->sum('tax_amount');
            $discount = (float)($this->discount_amount ?? 0);
            $total = max(0, $subtotal + $tax - $discount);
        } else {
            $subtotal = (float)$this->subtotal;
            $tax = (float)$this->tax_amount;
            $total = (float)$this->total_amount;
        }

        $paid = (float)$this->payments()->where('status', \App\Enums\PaymentStatus::COMPLETED->value)->sum('amount');
        $balance = max(0, $total - $paid);

        $status = $this->status;
        if ($this->status !== InvoiceStatus::CANCELLED) {
            if ($paid >= $total && $total > 0) {
                $status = InvoiceStatus::PAID;
            } elseif ($paid > 0 && $paid < $total) {
                $status = InvoiceStatus::PARTIALLY_PAID;
            } elseif ($this->due_date && $this->due_date->isPast() && $balance > 0) {
                $status = InvoiceStatus::OVERDUE;
            }
        }

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $total,
            'balance_due' => $balance,
            'status' => $status,
        ]);
    }
}
