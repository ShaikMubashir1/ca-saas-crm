<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;
    private Client $client1;
    private Client $client2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant1 = Tenant::create(['name' => 'Invoice Firm 1', 'domain' => 'inv1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Invoice Firm 2', 'domain' => 'inv2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA User 1',
            'email' => 'ca1@inv1.com',
            'password' => bcrypt('password'),
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'CA User 2',
            'email' => 'ca2@inv2.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Invoice Client 1',
            'phone' => '919800000001',
            'email' => 'client1@inv.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Invoice Client 2',
            'phone' => '919800000002',
            'email' => 'client2@inv.com',
        ]);
    }

    public function test_invoice_number_generation_is_unique_per_tenant()
    {
        $num1 = Invoice::generateNextInvoiceNumber($this->tenant1->id);
        $this->assertStringStartsWith('INV-' . date('Y') . '-', $num1);

        Invoice::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'invoice_number' => $num1,
            'subtotal' => 1000,
            'total_amount' => 1180,
            'balance_due' => 1180,
            'issue_date' => now(),
            'status' => InvoiceStatus::DRAFT,
        ]);

        $num2 = Invoice::generateNextInvoiceNumber($this->tenant1->id);
        $this->assertNotEquals($num1, $num2);

        // Tenant 2 starts its own sequence
        $numTenant2 = Invoice::generateNextInvoiceNumber($this->tenant2->id);
        $this->assertEquals($num1, $numTenant2);
    }

    public function test_create_invoice_livewire_component_and_calculations()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Invoices\Create::class)
            ->set('client_id', $this->client1->id)
            ->set('invoice_date', date('Y-m-d'))
            ->set('due_date', date('Y-m-d', strtotime('+15 days')))
            ->set('discount_amount', 100)
            ->set('items', [
                [
                    'service_id' => null,
                    'description' => 'Tax Audit Fees',
                    'quantity' => 1,
                    'unit_price' => 10000,
                    'tax_rate' => 18,
                ]
            ])
            ->call('save', 'sent')
            ->assertHasNoErrors();

        $inv = Invoice::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($inv);
        $this->assertEquals(10000, $inv->subtotal);
        $this->assertEquals(1800, $inv->tax_amount);
        $this->assertEquals(100, $inv->discount_amount);
        $this->assertEquals(11700, $inv->total_amount); // 10000 + 1800 - 100
        $this->assertEquals(11700, $inv->balance_due);
        $this->assertEquals(InvoiceStatus::SENT, $inv->status);
    }

    public function test_tenant_isolation_on_invoice_view()
    {
        $inv = Invoice::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'invoice_number' => 'INV-2026-9999',
            'subtotal' => 5000,
            'total_amount' => 5900,
            'balance_due' => 5900,
            'issue_date' => now(),
            'status' => InvoiceStatus::DRAFT,
        ]);

        // User 2 from Tenant 2 blocked by tenant global scope / policy
        $this->actingAs($this->user2);
        $response = $this->get(route('invoices.show', $inv->id));
        $this->assertTrue(in_array($response->status(), [403, 404]));
    }
}
