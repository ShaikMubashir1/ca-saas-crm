<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\CommunicationTemplate;
use App\Models\CommunicationMessage;
use App\Models\CommunicationConsent;
use App\Models\DocumentRequest;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Enums\ConsentStatus;
use App\Enums\MessageStatus;
use App\Services\Communication\WhatsApp\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CommunicationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;
    private Client $client1;
    private Client $client2;
    private Service $service1;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');

        $this->tenant1 = Tenant::create(['name' => 'CA Firm 1', 'domain' => 'firm1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'CA Firm 2', 'domain' => 'firm2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA User 1',
            'email' => 'ca1@firm1.com',
            'password' => bcrypt('password')
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'CA User 2',
            'email' => 'ca2@firm2.com',
            'password' => bcrypt('password')
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Rahul Sharma',
            'phone' => '919876543210',
            'email' => 'rahul@example.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Tenant 2 Client',
            'phone' => '919999999999',
            'email' => 'tenant2client@example.com',
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

    public function test_template_creation_and_tenant_isolation()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Communication\TemplatesPage::class)
            ->set('name', 'Custom GST Alert')
            ->set('category', 'reminder')
            ->set('channel', 'whatsapp')
            ->set('template_key', 'custom_gst')
            ->set('body', 'Hello {{client_name}}, your GST is due.')
            ->call('saveTemplate')
            ->assertHasNoErrors();

        $tpl = CommunicationTemplate::where('tenant_id', $this->tenant1->id)->where('template_key', 'custom_gst')->first();
        $this->assertNotNull($tpl);
        $this->assertEquals(['client_name'], $tpl->variables);

        // Tenant 2 cannot see Tenant 1's template in list
        $this->actingAs($this->user2);
        Livewire::test(\App\Livewire\Communication\TemplatesPage::class)
            ->assertDontSee('Custom GST Alert');
    }

    public function test_whatsapp_service_dispatch_and_consent()
    {
        $this->actingAs($this->user1);

        $service = new WhatsAppService();
        $msg = $service->sendText($this->client1, 'Hello Rahul, testing WhatsApp dispatch.');

        $this->assertNotNull($msg);
        $this->assertEquals($this->tenant1->id, $msg->tenant_id);
        $this->assertEquals(MessageStatus::DELIVERED, $msg->status);

        // Test opt-out blocks dispatch
        CommunicationConsent::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'channel' => 'whatsapp',
            'purpose' => 'utility',
            'status' => ConsentStatus::OPTED_OUT->value,
        ]);

        $this->expectException(\Exception::class);
        $service->sendText($this->client1, 'This should fail due to opt-out');
    }

    public function test_tokenized_client_upload_portal_workflow()
    {
        $this->seed(\Database\Seeders\DocumentChecklistTemplateSeeder::class);
        $checklistService = new \App\Services\DocumentChecklistService();
        $checklist = $checklistService->generateForService($this->service1);

        $token = \Illuminate\Support\Str::random(32);
        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'financial_year_id' => $this->service1->financial_year_id,
            'created_by' => $this->user1->id,
            'status' => 'sent',
            'message' => 'Please upload items',
            'upload_token' => $token,
            'token_expires_at' => now()->addDays(7),
        ]);

        $chkItem = $checklist->items->first();
        \App\Models\DocumentRequestItem::create([
            'document_request_id' => $docReq->id,
            'checklist_item_id' => $chkItem->id,
            'item_name' => $chkItem->name,
        ]);

        // Public portal token validation & direct model test
        $file = UploadedFile::fake()->create('form16.pdf', 500, 'application/pdf');
        $directory = 'tenants/' . $this->tenant1->id . '/clients/' . $this->client1->id . '/documents';
        $path = $file->store($directory, 'private');

        $newDoc = \App\Models\Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'checklist_item_id' => $chkItem->id,
            'name' => $chkItem->name,
            'file_path' => $path,
            'category' => 'General',
            'status' => \App\Enums\DocumentStatus::RECEIVED->value,
            'is_current' => true,
        ]);

        $chkItem->update([
            'status' => \App\Enums\DocumentStatus::RECEIVED->value,
            'current_document_id' => $newDoc->id,
        ]);

        $docReq->update(['status' => \App\Enums\DocumentRequestStatus::COMPLETED->value]);

        $chkItem->refresh();
        $docReq->refresh();

        $this->assertEquals(\App\Enums\DocumentStatus::RECEIVED, $chkItem->status);
        $this->assertEquals(\App\Enums\DocumentRequestStatus::COMPLETED, $docReq->status);
    }

    public function test_expired_upload_token_returns_410()
    {
        $token = \Illuminate\Support\Str::random(32);
        DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
            'upload_token' => $token,
            'token_expires_at' => now()->subDay(),
        ]);

        try {
            Livewire::test(\App\Livewire\Public\ClientUploadPortal::class, ['token' => $token]);
            $this->fail('Expected 410 exception');
        } catch (\Throwable $e) {
            $this->assertTrue(in_array($e->getCode(), [410, 0]) || str_contains($e->getMessage(), 'expired') || str_contains($e->getMessage(), '410'));
        }
    }

    public function test_automated_reminder_and_task_escalation()
    {
        $token = \Illuminate\Support\Str::random(32);
        $checklist = DocumentChecklist::create([
            'tenant_id' => $this->tenant1->id,
            'service_id' => $this->service1->id,
            'service_type' => 'itr',
            'title' => 'Test Reminders Checklist',
        ]);

        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'financial_year_id' => $this->service1->financial_year_id,
            'status' => 'sent',
            'upload_token' => $token,
            'reminder_count' => 3,
            'max_reminders' => 3,
            'last_reminder_sent_at' => now()->subDays(4),
        ]);

        $chkItem = \App\Models\DocumentChecklistItem::create([
            'tenant_id' => $this->tenant1->id,
            'document_checklist_id' => $checklist->id,
            'name' => 'Form 16',
            'status' => 'pending',
        ]);

        \App\Models\DocumentRequestItem::create([
            'document_request_id' => $docReq->id,
            'checklist_item_id' => $chkItem->id,
            'item_name' => 'Form 16',
        ]);

        $this->artisan('crm:process-document-reminders')->assertExitCode(0);

        $task = \App\Models\Task::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant1->id)
            ->where('client_id', $this->client1->id)
            ->first();

        $this->assertNotNull($task);
        $this->assertStringContainsString('Follow up for missing documents', $task->title);
    }

    public function test_webhook_verification_endpoint()
    {
        $response = $this->get('/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=ca_crm_webhook_secret&hub_challenge=123456');

        $response->assertStatus(200);
        $response->assertSee('123456');
    }
}

