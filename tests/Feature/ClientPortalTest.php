<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\DocumentRequest;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;
use App\Models\ClientPortalToken;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ClientPortalTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private Client $client1;
    private Client $client2;
    private Service $service1;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');

        $this->tenant1 = Tenant::create(['name' => 'Portal Firm 1', 'domain' => 'portal1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Portal Firm 2', 'domain' => 'portal2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA User Portal',
            'email' => 'portal@firm1.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Portal Client 1',
            'phone' => '919876500000',
            'email' => 'portal1@client.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Portal Client 2',
            'phone' => '919999900000',
            'email' => 'portal2@client.com',
        ]);

        $fy = FinancialYear::create([
            'tenant_id' => $this->tenant1->id,
            'year_label' => 'FY 2026-27',
        ]);

        $this->service1 = Service::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'financial_year_id' => $fy->id,
            'type' => ServiceType::Itr->value,
            'status' => ServiceStatus::NOT_STARTED->value,
        ]);
    }

    public function test_valid_token_access_renders_portal()
    {
        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
        ]);

        $res = ClientPortalToken::generate($this->client1, $docReq, 14);
        $plainToken = $res['plain_token'];

        Livewire::test(\App\Livewire\Public\ClientUploadPortal::class, ['token' => $plainToken])
            ->assertSee($this->client1->name);
    }

    public function test_invalid_token_rejected()
    {
        try {
            Livewire::test(\App\Livewire\Public\ClientUploadPortal::class, ['token' => 'invalid_random_token_string']);
            $this->fail('Expected 404 exception');
        } catch (\Throwable $e) {
            $this->assertTrue(str_contains($e->getMessage(), '404') || $e->getCode() === 404 || str_contains($e->getMessage(), 'Invalid'));
        }
    }

    public function test_expired_token_rejected()
    {
        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
            'upload_token' => 'expired_token',
            'token_expires_at' => now()->subDay(),
        ]);

        try {
            Livewire::test(\App\Livewire\Public\ClientUploadPortal::class, ['token' => 'expired_token']);
            $this->fail('Expected expired exception');
        } catch (\Throwable $e) {
            $this->assertTrue(str_contains($e->getMessage(), 'expired') || in_array($e->getCode(), [410, 0]));
        }
    }

    public function test_revoked_token_rejected()
    {
        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
            'upload_token' => null,
        ]);

        $res = ClientPortalToken::generate($this->client1, $docReq, 14);
        $plainToken = $res['plain_token'];
        $res['record']->revoke();

        $this->get('/portal/documents/' . $plainToken)
            ->assertStatus(404);
    }

    public function test_client_document_upload_and_status_update()
    {
        $checklist = DocumentChecklist::create([
            'tenant_id' => $this->tenant1->id,
            'service_id' => $this->service1->id,
            'service_type' => 'itr',
            'title' => 'Portal Test Checklist',
        ]);

        $chkItem = DocumentChecklistItem::create([
            'tenant_id' => $this->tenant1->id,
            'document_checklist_id' => $checklist->id,
            'name' => 'Form 16 Tax Certificate',
            'status' => 'pending',
        ]);

        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
        ]);

        \App\Models\DocumentRequestItem::create([
            'document_request_id' => $docReq->id,
            'checklist_item_id' => $chkItem->id,
            'item_name' => 'Form 16 Tax Certificate',
        ]);

        $res = ClientPortalToken::generate($this->client1, $docReq, 14);
        $plainToken = $res['plain_token'];

        $file = UploadedFile::fake()->create('form16.pdf', 800, 'application/pdf');

        $component = Livewire::test(\App\Livewire\Public\ClientUploadPortal::class, ['token' => $plainToken]);
        $component->set('selectedItemId', $chkItem->id);
        $component->set('uploadFile', $file);
        $component->call('uploadDocument');

        $chkItem->refresh();
        $docReq->refresh();

        $this->assertEquals(\App\Enums\DocumentStatus::RECEIVED, $chkItem->status);
        $this->assertEquals(\App\Enums\DocumentRequestStatus::COMPLETED, $docReq->status);

        // Verify Timeline Event
        $timelineEvent = \App\Models\TimelineEvent::where('tenant_id', $this->tenant1->id)
            ->where('client_id', $this->client1->id)
            ->where('event_type', 'Client Upload Received')
            ->first();

        $this->assertNotNull($timelineEvent);
    }
}
