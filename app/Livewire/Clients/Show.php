<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\Document;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Models\User;
use App\Models\TimelineEvent;
use App\Enums\ServiceType;
use App\Enums\ServiceStatus;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
class Show extends Component
{
    public Client $client;

    // Service Modal State
    public bool $showServiceModal = false;
    public string $service_type = 'itr';
    public ?int $financial_year_id = null;
    public string $status = 'not_started';
    public ?int $assigned_staff_id = null;
    public ?int $reviewer_id = null;
    public ?string $start_date = null;
    public ?string $deadline = null;
    public ?string $notes = null;

    // Communication Modal State
    public bool $showCommunicationModal = false;
    public string $messageBody = '';
    public ?int $selectedTemplateId = null;

    public function openCommunicationModal(): void
    {
        $this->messageBody = '';
        $this->selectedTemplateId = null;
        $this->showCommunicationModal = true;
    }

    public function updatedSelectedTemplateId($value): void
    {
        if ($value) {
            $tpl = \App\Models\CommunicationTemplate::find($value);
            if ($tpl) {
                $body = $tpl->body;
                $body = str_replace('{{client_name}}', $this->client->name, $body);
                $body = str_replace('{{firm_name}}', Auth::user()->tenant ? Auth::user()->tenant->name : 'CA Firm', $body);
                $body = str_replace('{{assigned_staff}}', Auth::user()->name, $body);
                $this->messageBody = $body;
            }
        }
    }

    public function sendClientMessage(): void
    {
        $this->validate([
            'messageBody' => 'required|string|min:3',
        ]);

        $wsService = new \App\Services\Communication\WhatsApp\WhatsAppService();
        
        try {
            $msg = $wsService->sendText($this->client, $this->messageBody);

            TimelineEvent::create([
                'tenant_id' => Auth::user()->tenant_id,
                'client_id' => $this->client->id,
                'user_id' => Auth::id(),
                'event_type' => 'WhatsApp Message Sent',
                'description' => "Dispatched message to {$this->client->name} (" . substr($this->messageBody, 0, 40) . "...)",
            ]);

            session()->flash('success', 'WhatsApp message dispatched successfully.');
            $this->showCommunicationModal = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function mount(Client $client)
    {
        // Security check: Ensure client belongs to authenticated user's tenant
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this client.');
        }

        $this->client = $client;
        
        // Default to active financial year or latest FY
        $defaultFy = FinancialYear::latest()->first();
        if ($defaultFy) {
            $this->financial_year_id = $defaultFy->id;
        }
    }

    public function openServiceModal(): void
    {
        $this->resetServiceForm();
        $this->showServiceModal = true;
    }

    public function closeServiceModal(): void
    {
        $this->showServiceModal = false;
        $this->resetServiceForm();
    }

    private function resetServiceForm(): void
    {
        $this->resetValidation();
        $this->service_type = 'itr';
        $defaultFy = FinancialYear::latest()->first();
        $this->financial_year_id = $defaultFy ? $defaultFy->id : null;
        $this->status = 'not_started';
        $this->assigned_staff_id = null;
        $this->reviewer_id = null;
        $this->start_date = null;
        $this->deadline = null;
        $this->notes = null;
    }

    public function addService()
    {
        $tenantId = Auth::user()->tenant_id;

        $validated = $this->validate([
            'service_type' => ['required', 'string', Rule::in(array_column(ServiceType::cases(), 'value'))],
            'financial_year_id' => [
                'required',
                'integer',
                Rule::exists('financial_years', 'id')->where('tenant_id', $tenantId),
            ],
            'status' => ['required', 'string', Rule::in(array_column(ServiceStatus::cases(), 'value'))],
            'assigned_staff_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('tenant_id', $tenantId),
            ],
            'reviewer_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('tenant_id', $tenantId),
            ],
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // Duplicate prevention check: Tenant + Client + FY + Service Type
        $exists = Service::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->where('financial_year_id', $validated['financial_year_id'])
            ->where('type', $validated['service_type'])
            ->exists();

        if ($exists) {
            $this->addError('service_type', 'This service already exists for the selected client and financial year.');
            return;
        }

        $service = Service::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->client->id,
            'financial_year_id' => $validated['financial_year_id'],
            'type' => $validated['service_type'],
            'status' => $validated['status'],
            'assigned_staff_id' => $validated['assigned_staff_id'],
            'reviewer_id' => $validated['reviewer_id'],
            'start_date' => $validated['start_date'],
            'deadline' => $validated['deadline'],
            'notes' => $validated['notes'],
        ]);

        // Create Timeline Event
        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->client->id,
            'user_id' => Auth::id(),
            'event_type' => 'Service Created',
            'description' => "Added " . $service->type->label() . " service for " . ($service->financialYear ? $service->financialYear->year_label : 'FY'),
        ]);

        // Generate Document Checklist for the service
        $checklistService = new \App\Services\DocumentChecklistService();
        $checklistService->generateForService($service);

        session()->flash('success', 'Service added successfully with default checklist.');
        $this->closeServiceModal();
    }

    public function updateServiceStatus(int $serviceId, string $newStatus)
    {
        $tenantId = Auth::user()->tenant_id;
        $service = Service::where('tenant_id', $tenantId)->where('client_id', $this->client->id)->findOrFail($serviceId);

        $oldStatusLabel = $service->status->label();
        $service->status = $newStatus;
        if ($newStatus === ServiceStatus::FILED->value || $newStatus === ServiceStatus::COMPLETED->value) {
            $service->filing_date = now();
        }
        $service->save();

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->client->id,
            'user_id' => Auth::id(),
            'event_type' => 'Service Status Updated',
            'description' => "Updated " . $service->type->label() . " status from '{$oldStatusLabel}' to '" . $service->status->label() . "'",
        ]);

        session()->flash('success', 'Service status updated successfully.');
    }

    public function downloadDocument($documentId)
    {
        $document = Document::where('tenant_id', Auth::user()->tenant_id)->findOrFail($documentId);

        if (!Storage::disk('public')->exists($document->file_path)) {
            session()->flash('error', 'File not found on storage.');
            return;
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function deleteDocument($documentId)
    {
        $document = Document::where('tenant_id', Auth::user()->tenant_id)->findOrFail($documentId);

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        session()->flash('success', 'Document deleted successfully.');
    }

    public function deleteCredential($credentialId)
    {
        $credential = \App\Models\ClientCredential::where('tenant_id', Auth::user()->tenant_id)->findOrFail($credentialId);
        $credential->delete();
        session()->flash('success', 'Credential deleted successfully.');
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        // Eager loading for Client 360
        $services = $this->client->services()
            ->with(['financialYear', 'assignedStaff', 'reviewer', 'checklist.items'])
            ->latest()
            ->get();

        $documents = $this->client->documents()->latest()->get();
        $credentials = $this->client->credentials()->latest()->get();
        $tasks = $this->client->tasks()->latest()->get();
        $timelineEvents = $this->client->timelineEvents()->with('user')->latest()->limit(15)->get();

        // Invoices summary (if exists)
        $invoices = \App\Models\Invoice::where('tenant_id', $tenantId)->where('client_id', $this->client->id)->get();

        // Staff members for modal dropdowns
        $staffMembers = User::where('tenant_id', $tenantId)->get();

        // Tenant Financial Years
        $financialYears = FinancialYear::where('tenant_id', $tenantId)->orderBy('year_label', 'desc')->get();

        // Group services by Financial Year for summary card
        $fySummary = $services->groupBy(function ($svc) {
            return $svc->financialYear ? $svc->financialYear->year_label : 'Unspecified FY';
        });

        // Provider-independent Communications & history
        $communications = \App\Models\Communication::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->with('user')
            ->latest()
            ->get();

        $commMetrics = [
            'total' => $communications->count(),
            'whatsapp' => $communications->where('channel', \App\Enums\CommunicationChannel::WHATSAPP)->count(),
            'email' => $communications->where('channel', \App\Enums\CommunicationChannel::EMAIL)->count(),
            'sms' => $communications->where('channel', \App\Enums\CommunicationChannel::SMS)->count(),
            'sent' => $communications->whereIn('status', [\App\Enums\CommunicationStatus::SENT, \App\Enums\CommunicationStatus::DELIVERED, \App\Enums\CommunicationStatus::READ])->count(),
            'delivered' => $communications->whereIn('status', [\App\Enums\CommunicationStatus::DELIVERED, \App\Enums\CommunicationStatus::READ])->count(),
            'failed' => $communications->where('status', \App\Enums\CommunicationStatus::FAILED)->count(),
            'last' => $communications->first(),
        ];

        // Billing metrics & history
        $invoices = \App\Models\Invoice::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->latest()
            ->get();

        $payments = \App\Models\Payment::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->latest()
            ->get();

        $billingMetrics = [
            'total_invoiced' => $invoices->sum('total_amount'),
            'total_paid' => $payments->where('status', \App\Enums\PaymentStatus::COMPLETED)->sum('amount'),
            'outstanding' => $invoices->where('status', '!=', \App\Enums\InvoiceStatus::CANCELLED)->sum('balance_due'),
            'overdue' => $invoices->where('status', \App\Enums\InvoiceStatus::OVERDUE)->sum('balance_due'),
        ];

        // Communication history & templates
        $communicationMessages = \App\Models\CommunicationMessage::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->with(['user', 'template'])
            ->latest()
            ->get();

        $communicationTemplates = \App\Models\CommunicationTemplate::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        // Compliance metrics & history
        $complianceInstances = \App\Models\ComplianceInstance::where('tenant_id', $tenantId)
            ->where('client_id', $this->client->id)
            ->with(['template', 'assignee'])
            ->orderBy('due_date', 'asc')
            ->get();

        $complianceMetrics = [
            'total' => $complianceInstances->count(),
            'upcoming' => $complianceInstances->where('status', \App\Enums\ComplianceStatus::UPCOMING)->count(),
            'overdue' => $complianceInstances->where('status', \App\Enums\ComplianceStatus::OVERDUE)->count(),
            'filed' => $complianceInstances->whereIn('status', [\App\Enums\ComplianceStatus::FILED, \App\Enums\ComplianceStatus::ACKNOWLEDGED])->count(),
        ];

        return view('livewire.clients.show', [
            'services' => $services,
            'documents' => $documents,
            'credentials' => $credentials,
            'tasks' => $tasks,
            'timelineEvents' => $timelineEvents,
            'staffMembers' => $staffMembers,
            'financialYears' => $financialYears,
            'fySummary' => $fySummary,
            'communications' => $communications,
            'commMetrics' => $commMetrics,
            'communicationMessages' => $communicationMessages,
            'communicationTemplates' => $communicationTemplates,
            'invoices' => $invoices,
            'payments' => $payments,
            'billingMetrics' => $billingMetrics,
            'complianceInstances' => $complianceInstances,
            'complianceMetrics' => $complianceMetrics,
        ]);
    }
}
