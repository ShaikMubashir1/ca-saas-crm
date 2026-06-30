<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_belongs_to_tenant_and_encrypts_sensitive_data()
    {
        $tenant1 = Tenant::create(['name' => 'Tenant 1', 'domain' => 'tenant1.com']);
        $tenant2 = Tenant::create(['name' => 'Tenant 2', 'domain' => 'tenant2.com']);

        $user = User::create([
            'tenant_id' => $tenant1->id,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $this->actingAs($user);

        $client = Client::create([
            'entity_type' => 'Individual',
            'name' => 'John Doe',
            'pan' => 'ABCDE1234F'
        ]);

        $this->assertEquals($tenant1->id, $client->tenant_id);
        
        $clients = Client::all();
        $this->assertCount(1, $clients);

        $this->assertNotEquals('ABCDE1234F', $client->getRawOriginal('pan'));
        $this->assertEquals('ABCDE1234F', $client->pan);

        $user2 = User::create([
            'tenant_id' => $tenant2->id,
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password')
        ]);
        
        $this->actingAs($user2);
        
        $clientsTenant2 = Client::all();
        $this->assertCount(0, $clientsTenant2);
    }
}
