<?php

namespace App\Livewire\Compliance;

use App\Models\ComplianceInstance;
use App\Models\Client;
use App\Models\FinancialYear;
use App\Models\User;
use App\Models\TimelineEvent;
use App\Enums\ComplianceStatus;
use App\Services\Compliance\ComplianceGenerator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = '';
    public ?int $selectedClient = null;
    public ?int $selectedFY = null;
    public string $selectedServiceType = '';

    // Status transition modal
    public bool $showStatusModal = false;
    public ?int $selectedInstanceId = null;
    public string $newStatus = '';
    public string $acknowledgementNumber = '';
    public string $filingDate = '';
    public string $transitionNotes = '';

    public function mount(): void
    {
        $this->filingDate = date('Y-m-d');
    }

    public function generateForClient(int $clientId, int $fyId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $client = Client::where('tenant_id', $tenantId)->findOrFail($clientId);
        $fy = FinancialYear::where('tenant_id', $tenantId)->findOrFail($fyId);

        $generator = new ComplianceGenerator();
        $created = $generator->generateForClient($client, $fy);

        session()->flash('success', "Generated " . count($created) . " compliance items for {$client->name}.");
    }

    public function openStatusModal(int $instanceId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $instance = ComplianceInstance::where('tenant_id', $tenantId)->findOrFail($instanceId);

        $this->selectedInstanceId = $instance->id;
        $this->newStatus = $instance->status->value;
        $this->acknowledgementNumber = $instance->acknowledgement_number ?? '';
        $this->filingDate = $instance->filing_date ? $instance->filing_date->format('Y-m-d') : date('Y-m-d');
        $this->showStatusModal = true;
    }

    public function updateStatus(): void
    {
        $tenantId = Auth::user()->tenant_id;
        $instance = ComplianceInstance::where('tenant_id', $tenantId)->findOrFail($this->selectedInstanceId);

        $oldStatusLabel = $instance->status->label();
        $targetEnum = ComplianceStatus::from($this->newStatus);

        $data = [
            'status' => $targetEnum,
            'notes' => $this->transitionNotes ?: $instance->notes,
        ];

        if ($targetEnum === ComplianceStatus::FILED || $targetEnum === ComplianceStatus::ACKNOWLEDGED) {
            $data['filing_date'] = $this->filingDate;
            if ($this->acknowledgementNumber) {
                $data['acknowledgement_number'] = $this->acknowledgementNumber;
            }
        }

        $instance->update($data);

        // Update corresponding task status if present
        if ($instance->task) {
            $taskStatus = match ($targetEnum) {
                ComplianceStatus::FILED, ComplianceStatus::ACKNOWLEDGED => 'completed',
                ComplianceStatus::IN_PREPARATION, ComplianceStatus::UNDER_REVIEW, ComplianceStatus::READY_TO_FILE => 'in_progress',
                default => 'pending',
            };
            $instance->task->update(['status' => $taskStatus]);
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $instance->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Compliance Status Updated',
            'description' => "Updated {$instance->template->code} ({$instance->period}) status from {$oldStatusLabel} to {$targetEnum->label()}",
        ]);

        session()->flash('success', "Updated compliance status to {$targetEnum->label()}.");
        $this->showStatusModal = false;
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        // Auto update overdue compliance instances (only if still upcoming or docs_pending)
        ComplianceInstance::where('tenant_id', $tenantId)
            ->whereIn('status', [ComplianceStatus::UPCOMING->value, ComplianceStatus::DOCS_PENDING->value])
            ->where('due_date', '<', date('Y-m-d'))
            ->update(['status' => ComplianceStatus::OVERDUE->value]);

        $instancesQuery = ComplianceInstance::where('tenant_id', $tenantId)
            ->with(['client', 'service', 'financialYear', 'template', 'assignee', 'reviewer'])
            ->when($this->search, function ($q) {
                $q->whereHas('client', fn($c) => $c->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('template', fn($t) => $t->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"));
            })
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->selectedClient, fn($q) => $q->where('client_id', $this->selectedClient))
            ->when($this->selectedFY, fn($q) => $q->where('financial_year_id', $this->selectedFY))
            ->when($this->selectedServiceType, fn($q) => $q->whereHas('template', fn($t) => $t->where('service_type', $this->selectedServiceType)))
            ->orderBy('due_date', 'asc');

        $allInstances = ComplianceInstance::where('tenant_id', $tenantId)->get();

        $metrics = [
            'total' => $allInstances->count(),
            'upcoming' => $allInstances->where('status', ComplianceStatus::UPCOMING)->count(),
            'docs_pending' => $allInstances->where('status', ComplianceStatus::DOCS_PENDING)->count(),
            'due_soon' => $allInstances->filter(fn($i) => $i->due_date && $i->due_date->diffInDays(now()) <= 7 && !in_array($i->status, [ComplianceStatus::FILED, ComplianceStatus::ACKNOWLEDGED, ComplianceStatus::CANCELLED]))->count(),
            'overdue' => $allInstances->where('status', ComplianceStatus::OVERDUE)->count(),
            'filed' => $allInstances->whereIn('status', [ComplianceStatus::FILED, ComplianceStatus::ACKNOWLEDGED])->count(),
        ];

        $clients = Client::where('tenant_id', $tenantId)->orderBy('name')->get();
        $financialYears = FinancialYear::where('tenant_id', $tenantId)->orderBy('year_label', 'desc')->get();

        return view('livewire.compliance.dashboard', [
            'instances' => $instancesQuery->paginate(15),
            'metrics' => $metrics,
            'clients' => $clients,
            'financialYears' => $financialYears,
        ]);
    }
}
