<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\ComplianceTemplate;
use App\Models\ComplianceInstance;
use App\Models\Task;
use App\Enums\ClientType;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use App\Enums\ComplianceStatus;
use App\Services\Compliance\ComplianceGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ComplianceTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;
    private Client $client1;
    private Client $client2;
    private FinancialYear $fy1;
    private Service $gstService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default compliance templates
        $this->seed(\Database\Seeders\ComplianceTemplateSeeder::class);

        $this->tenant1 = Tenant::create(['name' => 'Compliance Firm 1', 'domain' => 'comp1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Compliance Firm 2', 'domain' => 'comp2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA Compliance User 1',
            'email' => 'ca1@comp1.com',
            'password' => bcrypt('password'),
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'CA Compliance User 2',
            'email' => 'ca2@comp2.com',
            'password' => bcrypt('password'),
        ]);

        $this->client1 = Client::create([
            'tenant_id' => $this->tenant1->id,
            'entity_type' => 'Private Limited Company',
            'client_type' => ClientType::Company->value,
            'name' => 'Tech Corp Pvt Ltd',
            'phone' => '919811100001',
            'email' => 'tech@corp.com',
        ]);

        $this->client2 = Client::create([
            'tenant_id' => $this->tenant2->id,
            'entity_type' => 'Private Limited Company',
            'client_type' => ClientType::Company->value,
            'name' => 'Other Firm Client',
            'phone' => '919811100002',
            'email' => 'other@firm.com',
        ]);

        $this->fy1 = FinancialYear::create([
            'tenant_id' => $this->tenant1->id,
            'year_label' => 'FY 2026-27',
        ]);

        $this->gstService = Service::create([
            'tenant_id' => $this->tenant1->id,
            'client_id' => $this->client1->id,
            'financial_year_id' => $this->fy1->id,
            'type' => ServiceType::Gst->value,
            'status' => ServiceStatus::NOT_STARTED->value,
            'assigned_staff_id' => $this->user1->id,
        ]);
    }

    public function test_compliance_generator_creates_instances_and_tasks_idempotently()
    {
        $generator = new ComplianceGenerator();
        $instances = $generator->generateForClient($this->client1, $this->fy1);

        $this->assertNotEmpty($instances);
        $countFirstRun = ComplianceInstance::where('tenant_id', $this->tenant1->id)->count();
        $this->assertGreaterThan(0, $countFirstRun);

        // Verify task linkage
        $taskCount = Task::where('tenant_id', $this->tenant1->id)->count();
        $this->assertEquals($countFirstRun, $taskCount);

        // Idempotency check
        $instancesSecondRun = $generator->generateForClient($this->client1, $this->fy1);
        $this->assertEmpty($instancesSecondRun);

        $countSecondRun = ComplianceInstance::where('tenant_id', $this->tenant1->id)->count();
        $this->assertEquals($countFirstRun, $countSecondRun);
    }

    public function test_compliance_status_workflow_transition_and_task_sync()
    {
        $generator = new ComplianceGenerator();
        $generator->generateForClient($this->client1, $this->fy1);

        $instance = ComplianceInstance::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($instance);

        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Compliance\Dashboard::class)
            ->set('selectedInstanceId', $instance->id)
            ->set('newStatus', ComplianceStatus::IN_PREPARATION->value)
            ->set('transitionNotes', 'Started data reconciliation')
            ->call('updateStatus')
            ->assertHasNoErrors();

        $instance->refresh();
        $this->assertEquals(ComplianceStatus::IN_PREPARATION, $instance->status);
        $this->assertEquals('in_progress', $instance->task->status);
    }

    public function test_tenant_isolation_on_compliance_instances()
    {
        $generator = new ComplianceGenerator();
        $generator->generateForClient($this->client1, $this->fy1);

        $instance = ComplianceInstance::where('tenant_id', $this->tenant1->id)->first();

        // User from Tenant 2 cannot see Tenant 1 instance
        $this->actingAs($this->user2);
        $this->assertFalse($this->user2->can('view', $instance));
    }
}
