<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\User;
use App\Models\FinancialYear;
use App\Models\Service;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Livewire\Clients\Index;
use App\Livewire\Clients\Show;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant1 = Tenant::create(['name' => 'Tenant 1', 'domain' => 'tenant1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Tenant 2', 'domain' => 'tenant2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password')
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password')
        ]);
    }

    public function test_client_belongs_to_tenant_and_encrypts_sensitive_data()
    {
        $this->actingAs($this->user1);

        $client = Client::create([
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'John Doe',
            'pan' => 'ABCDE1234F'
        ]);

        $this->assertEquals($this->tenant1->id, $client->tenant_id);
        $this->assertCount(1, Client::all());
        $this->assertNotEquals('ABCDE1234F', $client->getRawOriginal('pan'));
        $this->assertEquals('ABCDE1234F', $client->pan);

        $this->actingAs($this->user2);
        $this->assertCount(0, Client::all());
    }

    public function test_client_list_search_and_tenant_isolation()
    {
        $this->actingAs($this->user1);

        $clientA = Client::create([
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Alpha Solutions',
            'pan' => 'AAAAA1111A'
        ]);

        $clientB = Client::create([
            'entity_type' => 'Company',
            'client_type' => ClientType::Company->value,
            'name' => 'Beta Corp',
            'pan' => 'BBBBB2222B'
        ]);

        Livewire::test(Index::class)
            ->assertSee('Alpha Solutions')
            ->assertSee('Beta Corp')
            ->set('search', 'Alpha')
            ->assertSee('Alpha Solutions')
            ->assertDontSee('Beta Corp')
            ->set('search', '')
            ->set('clientType', 'company')
            ->assertSee('Beta Corp')
            ->assertDontSee('Alpha Solutions');

        // Tenant 2 isolation
        $this->actingAs($this->user2);

        Livewire::test(Index::class)
            ->assertDontSee('Alpha Solutions')
            ->assertDontSee('Beta Corp');
    }

    public function test_client_360_access_and_tenant_security()
    {
        $this->actingAs($this->user1);

        $client = Client::create([
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Client 360 Test',
        ]);

        Livewire::test(Show::class, ['client' => $client])
            ->assertSee('Client 360 Test')
            ->assertStatus(200);

        // Tenant 2 should be forbidden from accessing Tenant 1 client
        $this->actingAs($this->user2);

        $response = $this->get(route('clients.show', $client->id));
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
    }

    public function test_add_service_to_client_and_duplicate_prevention()
    {
        $this->actingAs($this->user1);

        $client = Client::create([
            'entity_type' => 'Company',
            'client_type' => ClientType::Company->value,
            'name' => 'Service Client',
        ]);

        $fy = FinancialYear::create([
            'tenant_id' => $this->tenant1->id,
            'year_label' => 'FY 2026-27'
        ]);

        Livewire::test(Show::class, ['client' => $client])
            ->call('openServiceModal')
            ->set('service_type', ServiceType::Itr->value)
            ->set('financial_year_id', $fy->id)
            ->set('status', ServiceStatus::NOT_STARTED->value)
            ->call('addService')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('services', [
            'tenant_id' => $this->tenant1->id,
            'client_id' => $client->id,
            'financial_year_id' => $fy->id,
            'type' => 'itr',
        ]);

        // Attempting duplicate service
        Livewire::test(Show::class, ['client' => $client])
            ->set('service_type', ServiceType::Itr->value)
            ->set('financial_year_id', $fy->id)
            ->set('status', ServiceStatus::NOT_STARTED->value)
            ->call('addService')
            ->assertHasErrors(['service_type']);
    }
}
