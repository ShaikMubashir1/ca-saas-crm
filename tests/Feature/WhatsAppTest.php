<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use App\Models\WhatsAppConsent;
use App\Enums\ClientType;
use App\Enums\WhatsAppConversationStatus;
use App\Enums\WhatsAppMessageDirection;
use App\Enums\WhatsAppMessageStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WhatsAppTest extends TestCase
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

        $this->seed(\Database\Seeders\WhatsAppTemplateSeeder::class);

        $this->tenant1 = Tenant::create(['name' => 'WA Firm 1', 'domain' => 'wa1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'WA Firm 2', 'domain' => 'wa2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'WA User 1',
            'email' => 'wa1@firm1.com',
            'password' => bcrypt('password'),
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'WA User 2',
            'email' => 'wa2@firm2.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'WA Client 1',
            'phone' => '919888000001',
            'email' => 'waclient1@test.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'WA Client 2',
            'phone' => '919888000002',
            'email' => 'waclient2@test.com',
        ]);
    }

    public function test_conversation_creation_and_outbound_messaging_in_mock_mode()
    {
        $this->actingAs($this->user1);

        $conv = WhatsAppConversation::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'phone_number' => $this->client1->phone,
            'status' => WhatsAppConversationStatus::OPEN,
        ]);

        Livewire::test(\App\Livewire\WhatsApp\Inbox::class, ['conversation' => $conv->id])
            ->set('messageText', 'Hello, this is a mock test message.')
            ->call('sendMessage')
            ->assertHasNoErrors();

        $msg = WhatsAppMessage::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($msg);
        $this->assertEquals(WhatsAppMessageDirection::OUTBOUND, $msg->direction);
        $this->assertEquals(WhatsAppMessageStatus::DELIVERED, $msg->status);
    }

    public function test_inbound_webhook_processing_and_stop_opt_out()
    {
        $payload = [
            'tenant_id' => $this->tenant1->id,
            'from' => $this->client1->phone,
            'body' => 'STOP',
            'id' => 'wa_in_999',
        ];

        $response = $this->postJson('/webhooks/whatsapp', $payload);
        $response->assertStatus(200);

        $consent = WhatsAppConsent::where('tenant_id', $this->tenant1->id)
            ->where('client_id', $this->client1->id)
            ->first();

        $this->assertNotNull($consent);
        $this->assertFalse($consent->marketing_opt_in);
        $this->assertFalse($consent->transactional_opt_in);
    }

    public function test_tenant_isolation_on_whatsapp_inbox()
    {
        $conv1 = WhatsAppConversation::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'phone_number' => $this->client1->phone,
            'status' => WhatsAppConversationStatus::OPEN,
        ]);

        // User 2 from Tenant 2 blocked by policy / scoping
        $this->actingAs($this->user2);
        $this->assertFalse($this->user2->can('view', $conv1));
    }
}
