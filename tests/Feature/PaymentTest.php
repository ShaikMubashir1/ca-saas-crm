<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Enums\ClientType;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private Client $client1;
    private Invoice $invoice1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant1 = Tenant::create(['name' => 'Payment Firm 1', 'domain' => 'pay1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Payment Firm 2', 'domain' => 'pay2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA User Payment',
            'email' => 'ca@pay1.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Payment Client 1',
            'phone' => '919877700001',
            'email' => 'client1@pay.com',
        ]);

        $this->invoice1 = Invoice::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'invoice_number' => 'INV-2026-0001',
            'subtotal' => 10000,
            'tax_amount' => 1800,
            'total_amount' => 11800,
            'balance_due' => 11800,
            'issue_date' => now(),
            'status' => InvoiceStatus::SENT,
        ]);
    }

    public function test_partial_payment_recalculates_balance_and_status()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Invoices\Show::class, ['invoice' => $this->invoice1])
            ->set('paymentAmount', 5000)
            ->set('paymentDate', date('Y-m-d'))
            ->set('paymentMethod', PaymentMethod::BANK_TRANSFER->value)
            ->set('referenceNumber', 'UTR1234567')
            ->call('recordPayment')
            ->assertHasNoErrors();

        $this->invoice1->refresh();
        $this->assertEquals(6800, $this->invoice1->balance_due);
        $this->assertEquals(InvoiceStatus::PARTIALLY_PAID, $this->invoice1->status);

        $pmt = Payment::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($pmt);
        $this->assertEquals(5000, $pmt->amount);
    }

    public function test_full_payment_marks_invoice_paid()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Invoices\Show::class, ['invoice' => $this->invoice1])
            ->set('paymentAmount', 11800)
            ->set('paymentDate', date('Y-m-d'))
            ->set('paymentMethod', PaymentMethod::UPI->value)
            ->call('recordPayment')
            ->assertHasNoErrors();

        $this->invoice1->refresh();
        $this->assertEquals(0, $this->invoice1->balance_due);
        $this->assertEquals(InvoiceStatus::PAID, $this->invoice1->status);
    }

    public function test_payment_greater_than_balance_rejected()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Invoices\Show::class, ['invoice' => $this->invoice1])
            ->set('paymentAmount', 20000)
            ->set('paymentDate', date('Y-m-d'))
            ->set('paymentMethod', PaymentMethod::CASH->value)
            ->call('recordPayment')
            ->assertHasErrors(['paymentAmount']);
    }
}
