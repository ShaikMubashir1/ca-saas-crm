<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TimelineEvent;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use App\Services\Communication\CommunicationService;
use App\Enums\CommunicationChannel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public Invoice $invoice;

    // Record Payment Modal State
    public bool $showPaymentModal = false;
    public float $paymentAmount = 0.00;
    public string $paymentDate = '';
    public string $paymentMethod = 'bank_transfer';
    public string $referenceNumber = '';
    public string $paymentNotes = '';

    public function mount(Invoice $invoice): void
    {
        if ($invoice->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $this->invoice = $invoice;
        $this->paymentDate = date('Y-m-d');
        $this->paymentAmount = (float)$invoice->balance_due;
    }

    public function openPaymentModal(): void
    {
        $this->paymentAmount = (float)$this->invoice->balance_due;
        $this->paymentDate = date('Y-m-d');
        $this->showPaymentModal = true;
    }

    public function recordPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01|max:' . max(0.01, $this->invoice->balance_due),
            'paymentDate' => 'required|date',
            'paymentMethod' => 'required|string',
            'referenceNumber' => 'nullable|string|max:255',
            'paymentNotes' => 'nullable|string',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $amount = (float)$this->paymentAmount;

        $payment = Payment::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->invoice->client_id,
            'invoice_id' => $this->invoice->id,
            'received_by' => Auth::id(),
            'amount' => $amount,
            'payment_date' => $this->paymentDate,
            'method' => $this->paymentMethod,
            'reference_number' => $this->referenceNumber,
            'status' => PaymentStatus::COMPLETED,
            'notes' => $this->paymentNotes,
        ]);

        // Recalculate Invoice balance and status
        $this->invoice->recalculateTotals();

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->invoice->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Payment Recorded',
            'description' => "Recorded payment of ₹" . number_format($amount, 2) . " via " . strtoupper($this->paymentMethod) . " for Invoice {$this->invoice->invoice_number}",
        ]);

        // Send payment receipt WhatsApp notification
        try {
            $commService = new CommunicationService();
            $msgBody = "Dear {$this->invoice->client->name}, payment of ₹" . number_format($amount, 2) . " received for Invoice {$this->invoice->invoice_number}. Remaining balance: ₹" . number_format($this->invoice->balance_due, 2) . ". Thank you!";
            $commService->send(
                client: $this->invoice->client,
                channel: CommunicationChannel::WHATSAPP,
                message: $msgBody,
                subject: "Payment Receipt - Invoice {$this->invoice->invoice_number}",
                metadata: ['invoice_id' => $this->invoice->id, 'payment_id' => $payment->id]
            );
        } catch (\Exception $e) {
            session()->flash('warning', 'Payment recorded, but receipt notification skipped: ' . $e->getMessage());
        }

        session()->flash('success', 'Payment recorded successfully.');
        $this->showPaymentModal = false;
        $this->invoice->refresh();
    }

    public function sendInvoice(): void
    {
        if ($this->invoice->status === InvoiceStatus::DRAFT) {
            $this->invoice->update(['status' => InvoiceStatus::SENT]);
        }

        try {
            $commService = new CommunicationService();
            $msgBody = "Dear {$this->invoice->client->name}, Invoice {$this->invoice->invoice_number} of ₹" . number_format($this->invoice->total_amount, 2) . " is issued with due date {$this->invoice->due_date?->format('d M Y')}. Please remit payment at your earliest convenience.";
            $commService->send(
                client: $this->invoice->client,
                channel: CommunicationChannel::WHATSAPP,
                message: $msgBody,
                subject: "Invoice {$this->invoice->invoice_number}",
                metadata: ['invoice_id' => $this->invoice->id]
            );
            session()->flash('success', 'Invoice notification sent successfully.');
        } catch (\Exception $e) {
            session()->flash('warning', 'Invoice status updated, but WhatsApp dispatch skipped: ' . $e->getMessage());
        }

        $this->invoice->refresh();
    }

    public function cancelInvoice(): void
    {
        $this->invoice->update(['status' => InvoiceStatus::CANCELLED]);

        TimelineEvent::create([
            'tenant_id' => Auth::user()->tenant_id,
            'client_id' => $this->invoice->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Invoice Cancelled',
            'description' => "Cancelled invoice {$this->invoice->invoice_number}",
        ]);

        session()->flash('success', 'Invoice cancelled.');
        $this->invoice->refresh();
    }

    public function render()
    {
        $this->invoice->load(['client', 'items.service', 'payments.receivedBy', 'creator']);

        return view('livewire.invoices.show');
    }
}
