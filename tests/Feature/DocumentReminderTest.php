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
use App\Models\Communication;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Enums\CommunicationStatus;
use App\Enums\CommunicationChannel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentReminderTest extends TestCase
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

        $this->tenant1 = Tenant::create(['name' => 'Reminder Firm 1', 'domain' => 'reminder1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Reminder Firm 2', 'domain' => 'reminder2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'Reminder User',
            'email' => 'reminder@firm1.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Reminder Client 1',
            'phone' => '919876599999',
            'email' => 'reminder1@client.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Reminder Client 2',
            'phone' => '919999988888',
            'email' => 'reminder2@client.com',
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

    public function test_due_request_reminder_detection_and_communication_creation()
    {
        $checklist = DocumentChecklist::create([
            'tenant_id' => $this->tenant1->id,
            'service_id' => $this->service1->id,
            'service_type' => 'itr',
            'title' => 'Reminder Checklist',
        ]);

        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'financial_year_id' => $this->service1->financial_year_id,
            'status' => 'sent',
            'reminder_count' => 0,
            'max_reminders' => 3,
            'last_reminder_sent_at' => null,
        ]);

        $chkItem = DocumentChecklistItem::create([
            'tenant_id' => $this->tenant1->id,
            'document_checklist_id' => $checklist->id,
            'name' => 'Bank Statement',
            'status' => 'pending',
        ]);

        \App\Models\DocumentRequestItem::create([
            'document_request_id' => $docReq->id,
            'checklist_item_id' => $chkItem->id,
            'item_name' => 'Bank Statement',
        ]);

        $this->artisan('crm:process-document-reminders')->assertExitCode(0);

        $docReq->refresh();
        $this->assertEquals(1, $docReq->reminder_count);

        $comm = Communication::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant1->id)
            ->where('client_id', $this->client1->id)
            ->first();

        $this->assertNotNull($comm);
        $this->assertEquals(CommunicationChannel::WHATSAPP, $comm->channel);
        $this->assertEquals(CommunicationStatus::DELIVERED, $comm->status);
    }

    public function test_duplicate_reminder_prevented_within_interval()
    {
        $checklist = DocumentChecklist::create([
            'tenant_id' => $this->tenant1->id,
            'service_id' => $this->service1->id,
            'service_type' => 'itr',
            'title' => 'Interval Checklist',
        ]);

        $docReq = DocumentRequest::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'status' => 'sent',
            'reminder_count' => 1,
            'max_reminders' => 3,
            'last_reminder_sent_at' => now()->subDay(), // Only 1 day ago (interval is 3 days)
        ]);

        $chkItem = DocumentChecklistItem::create([
            'tenant_id' => $this->tenant1->id,
            'document_checklist_id' => $checklist->id,
            'name' => 'GST Return Draft',
            'status' => 'pending',
        ]);

        \App\Models\DocumentRequestItem::create([
            'document_request_id' => $docReq->id,
            'checklist_item_id' => $chkItem->id,
            'item_name' => 'GST Return Draft',
        ]);

        $this->artisan('crm:process-document-reminders')->assertExitCode(0);

        $docReq->refresh();
        $this->assertEquals(1, $docReq->reminder_count); // Count must not increase
    }
}
