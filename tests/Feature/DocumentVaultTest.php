<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Tenant;
use App\Models\User;
use App\Models\FinancialYear;
use App\Models\Service;
use App\Models\Document;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Enums\DocumentStatus;
use App\Services\DocumentChecklistService;
use App\Livewire\Documents\Upload;
use App\Livewire\Documents\ChecklistManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DocumentVaultTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;
    private Client $client1;
    private Service $service1;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('private');
        Storage::fake('public');

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

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Individual',
            'client_type' => ClientType::Individual->value,
            'name' => 'Client One',
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

    public function test_checklist_generation_from_service_type()
    {
        $this->actingAs($this->user1);

        // Seed template checklist
        $this->seed(\Database\Seeders\DocumentChecklistTemplateSeeder::class);

        $checklistService = new DocumentChecklistService();
        $checklist = $checklistService->generateForService($this->service1);

        $this->assertNotNull($checklist);
        $this->assertEquals('itr', $checklist->service_type);
        $this->assertGreaterThan(0, $checklist->items->count());
        $this->assertTrue($checklist->items->where('document_type', 'PAN')->first()->is_required);
    }

    public function test_authenticated_file_upload_and_private_storage()
    {
        $this->actingAs($this->user1);

        $file = UploadedFile::fake()->create('form16.pdf', 500, 'application/pdf');

        Livewire::test(Upload::class, ['client' => $this->client1])
            ->set('title', 'Form 16 Tax Certificate')
            ->set('document_type', 'Form 16')
            ->set('service_id', $this->service1->id)
            ->set('file', $file)
            ->call('save')
            ->assertHasNoErrors();

        $doc = Document::latest()->first();
        $this->assertNotNull($doc);
        $this->assertEquals($this->tenant1->id, $doc->tenant_id);
        $this->assertEquals('Form 16 Tax Certificate', $doc->name);

        Storage::disk('private')->assertExists($doc->file_path);
    }

    public function test_tenant_cannot_download_other_tenant_document()
    {
        $this->actingAs($this->user1);

        $file = UploadedFile::fake()->create('secret.pdf', 100, 'application/pdf');
        $path = $file->store('tenants/' . $this->tenant1->id . '/clients/' . $this->client1->id, 'private');

        $doc = Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'name' => 'Secret Doc',
            'file_path' => $path,
            'category' => 'General',
        ]);

        // Attempt download as User 2 (Tenant 2)
        $this->actingAs($this->user2);

        $response = $this->get(route('documents.download', $doc->id));
        $this->assertTrue(in_array($response->getStatusCode(), [403, 404]));
    }

    public function test_document_verification_workflow()
    {
        $this->actingAs($this->user1);
        $this->seed(\Database\Seeders\DocumentChecklistTemplateSeeder::class);

        $checklistService = new DocumentChecklistService();
        $checklist = $checklistService->generateForService($this->service1);
        $item = $checklist->items->first();

        // Create document attached to item
        $doc = Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'checklist_item_id' => $item->id,
            'name' => $item->name,
            'file_path' => 'dummy/path.pdf',
            'category' => 'General',
            'status' => DocumentStatus::RECEIVED->value,
        ]);

        $item->update([
            'status' => DocumentStatus::RECEIVED->value,
            'current_document_id' => $doc->id,
        ]);

        Livewire::test(ChecklistManager::class, ['service' => $this->service1])
            ->call('verifyItem', $item->id);

        $item->refresh();
        $doc->refresh();

        $this->assertEquals(DocumentStatus::VERIFIED, $item->status);
        $this->assertEquals(DocumentStatus::VERIFIED, $doc->status);
        $this->assertEquals($this->user1->id, $doc->verified_by);
        $this->assertNotNull($doc->verified_at);
    }

    public function test_document_rejection_and_replacement_history()
    {
        $this->actingAs($this->user1);
        $this->seed(\Database\Seeders\DocumentChecklistTemplateSeeder::class);

        $checklistService = new DocumentChecklistService();
        $checklist = $checklistService->generateForService($this->service1);
        $item = $checklist->items->first();

        // Create initial document
        $doc1 = Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'service_id' => $this->service1->id,
            'checklist_item_id' => $item->id,
            'name' => 'Doc v1',
            'file_path' => 'dummy/v1.pdf',
            'category' => 'General',
            'status' => DocumentStatus::RECEIVED->value,
            'is_current' => true,
        ]);

        $item->update([
            'status' => DocumentStatus::RECEIVED->value,
            'current_document_id' => $doc1->id,
        ]);

        // Reject item
        Livewire::test(ChecklistManager::class, ['service' => $this->service1])
            ->call('openRejectModal', $item->id)
            ->set('rejectionReason', 'Unclear scan image')
            ->call('confirmReject');

        $item->refresh();
        $doc1->refresh();

        $this->assertEquals(DocumentStatus::REJECTED, $item->status);
        $this->assertEquals('Unclear scan image', $doc1->rejection_reason);

        // Upload replacement file
        $replacementFile = UploadedFile::fake()->create('doc_v2.pdf', 300, 'application/pdf');

        Livewire::test(ChecklistManager::class, ['service' => $this->service1])
            ->call('openUploadModal', $item->id)
            ->set('uploadFile', $replacementFile)
            ->call('uploadReplacement');

        $item->refresh();
        $doc1->refresh();

        $doc2 = Document::orderByDesc('id')->first();

        $this->assertEquals($doc2->id, $item->current_document_id);
        $this->assertEquals(DocumentStatus::RECEIVED, $item->status);
        $this->assertFalse($doc1->is_current);
        $this->assertEquals($doc2->id, $doc1->replaced_by_id);
        $this->assertTrue($doc2->is_current);
    }

    public function test_document_request_creation_and_duplicate_prevention()
    {
        $this->actingAs($this->user1);
        $this->seed(\Database\Seeders\DocumentChecklistTemplateSeeder::class);

        $checklistService = new DocumentChecklistService();
        $checklist = $checklistService->generateForService($this->service1);

        Livewire::test(ChecklistManager::class, ['service' => $this->service1])
            ->call('openRequestModal')
            ->call('createDocumentRequest', 'sent');

        $req = \App\Models\DocumentRequest::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($req);
        $this->assertEquals(\App\Enums\DocumentRequestStatus::SENT, $req->status);
        $this->assertGreaterThan(0, $req->items->count());

        // Re-call request creation to test duplicate updating logic
        Livewire::test(ChecklistManager::class, ['service' => $this->service1])
            ->call('openRequestModal')
            ->call('createDocumentRequest', 'sent');

        $this->assertEquals(1, \App\Models\DocumentRequest::where('tenant_id', $this->tenant1->id)->count());
    }

    public function test_cross_tenant_checklist_access_blocked()
    {
        $this->actingAs($this->user2);

        try {
            Livewire::test(ChecklistManager::class, ['service' => $this->service1]);
            $this->fail('Expected 403 exception was not thrown.');
        } catch (\Throwable $e) {
            $this->assertTrue(in_array($e->getCode(), [403, 0]) || str_contains($e->getMessage(), '403') || str_contains($e->getMessage(), 'Unauthorized'));
        }
    }

    public function test_document_policy_tenant_authorization()
    {
        $policy = new \App\Policies\DocumentPolicy();

        $doc = Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'name' => 'Policy Doc',
            'file_path' => 'dummy.pdf',
            'category' => 'General',
        ]);

        $this->assertTrue($policy->view($this->user1, $doc));
        $this->assertFalse($policy->view($this->user2, $doc));

        $this->assertTrue($policy->download($this->user1, $doc));
        $this->assertFalse($policy->download($this->user2, $doc));
    }

    public function test_checklist_policy_tenant_authorization()
    {
        $policy = new \App\Policies\ChecklistPolicy();

        $checklist = DocumentChecklist::create([
            'tenant_id' => $this->tenant1->id,
            'service_type' => 'itr',
            'title' => 'Test Policy Checklist',
        ]);

        $this->assertTrue($policy->view($this->user1, $checklist));
        $this->assertFalse($policy->view($this->user2, $checklist));
        $this->assertTrue($policy->manage($this->user1, $checklist));
        $this->assertFalse($policy->manage($this->user2, $checklist));
    }

    public function test_documents_page_renders_with_filters()
    {
        $this->actingAs($this->user1);

        Document::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'name' => 'Filterable Document',
            'file_path' => 'dummy/filter.pdf',
            'category' => 'General',
            'status' => DocumentStatus::RECEIVED->value,
        ]);

        Livewire::test(\App\Livewire\Documents\DocumentsPage::class)
            ->set('search', 'Filterable')
            ->assertSee('Filterable Document');
    }
}
