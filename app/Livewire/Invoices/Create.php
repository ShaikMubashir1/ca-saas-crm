<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\InvoiceItem;
use App\Models\TimelineEvent;
use App\Enums\InvoiceStatus;
use App\Services\Communication\CommunicationService;
use App\Enums\CommunicationChannel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public ?int $client_id = null;
    public ?int $financial_year_id = null;
    public string $invoice_date = '';
    public string $due_date = '';
    public string $notes = '';
    public string $terms = '';
    public float $discount_amount = 0.00;

    // Line items
    public array $items = [];

    public function mount(): void
    {
        $this->invoice_date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime('+15 days'));
        $this->terms = 'Payment due within 15 days of invoice date. Late payments subject to interest.';
        
        // Add initial empty line item
        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = [
            'service_id' => '',
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0.00,
            'tax_rate' => 18.00,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function updatedItems($value, $key): void
    {
        // When a service is selected for an item, prefill description and rate
        if (str_contains($key, 'service_id') && !empty($value)) {
            $parts = explode('.', $key);
            $index = (int)$parts[0];
            $svc = Service::where('tenant_id', Auth::user()->tenant_id)->find($value);
            if ($svc) {
                $this->items[$index]['description'] = "Professional Fees - " . strtoupper($svc->type->value);
                $this->items[$index]['unit_price'] = 2500.00; // Standard default CA fee
            }
        }
    }

    public function calculateSubtotal(): float
    {
        $subtotal = 0;
        foreach ($this->items as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['unit_price'] ?? 0);
            $subtotal += ($qty * $price);
        }
        return $subtotal;
    }

    public function calculateTax(): float
    {
        $taxTotal = 0;
        foreach ($this->items as $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['unit_price'] ?? 0);
            $rate = (float)($item['tax_rate'] ?? 0);
            $amount = $qty * $price;
            $taxTotal += ($amount * ($rate / 100));
        }
        return $taxTotal;
    }

    public function calculateTotal(): float
    {
        return max(0, $this->calculateSubtotal() + $this->calculateTax() - (float)$this->discount_amount);
    }

    public function save(string $targetStatus = 'draft')
    {
        $this->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'required|numeric|min:0',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $client = Client::where('tenant_id', $tenantId)->findOrFail($this->client_id);

        $invNumber = Invoice::generateNextInvoiceNumber($tenantId);
        $statusEnum = $targetStatus === 'sent' ? InvoiceStatus::SENT : InvoiceStatus::DRAFT;

        $subtotal = $this->calculateSubtotal();
        $taxAmount = $this->calculateTax();
        $totalAmount = $this->calculateTotal();

        $invoice = Invoice::create([
            'tenant_id' => $tenantId,
            'client_id' => $client->id,
            'created_by' => Auth::id(),
            'invoice_number' => $invNumber,
            'subtotal' => $subtotal,
            'discount_amount' => (float)$this->discount_amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'balance_due' => $totalAmount,
            'issue_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'status' => $statusEnum,
            'notes' => $this->notes,
            'terms' => $this->terms,
        ]);

        foreach ($this->items as $itemData) {
            $qty = (float)$itemData['quantity'];
            $price = (float)$itemData['unit_price'];
            $rate = (float)$itemData['tax_rate'];
            $amt = $qty * $price;
            $taxAmt = $amt * ($rate / 100);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => !empty($itemData['service_id']) ? (int)$itemData['service_id'] : null,
                'description' => $itemData['description'],
                'quantity' => $qty,
                'unit_price' => $price,
                'amount' => $amt,
                'tax_rate' => $rate,
                'tax_amount' => $taxAmt,
            ]);
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'event_type' => 'Invoice Created',
            'description' => "Created invoice {$invNumber} for ₹" . number_format($totalAmount, 2) . " ({$statusEnum->label()})",
        ]);

        if ($statusEnum === InvoiceStatus::SENT) {
            try {
                $commService = new CommunicationService();
                $msgBody = "Dear {$client->name}, Invoice {$invNumber} of ₹" . number_format($totalAmount, 2) . " has been issued with due date {$this->due_date}. Thank you for your business!";
                $commService->send(
                    client: $client,
                    channel: CommunicationChannel::WHATSAPP,
                    message: $msgBody,
                    subject: "Invoice {$invNumber} Issued",
                    metadata: ['invoice_id' => $invoice->id]
                );
            } catch (\Exception $e) {
                session()->flash('warning', "Invoice {$invNumber} created, but WhatsApp dispatch skipped: " . $e->getMessage());
            }
        }

        session()->flash('success', "Invoice {$invNumber} created successfully.");
        return redirect()->route('invoices.show', $invoice->id);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $clients = Client::where('tenant_id', $tenantId)->orderBy('name')->get();
        $financialYears = FinancialYear::where('tenant_id', $tenantId)->orderBy('year_label', 'desc')->get();
        $services = $this->client_id 
            ? Service::where('tenant_id', $tenantId)->where('client_id', $this->client_id)->latest()->get()
            : collect();

        return view('livewire.invoices.create', [
            'clients' => $clients,
            'financialYears' => $financialYears,
            'services' => $services,
        ]);
    }
}
