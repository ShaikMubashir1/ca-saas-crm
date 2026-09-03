<?php

namespace App\Livewire\Documents;

use App\Models\Service;
use App\Models\DocumentChecklist;
use App\Models\DocumentChecklistItem;
use App\Models\Document;
use App\Models\TimelineEvent;
use App\Enums\DocumentStatus;
use App\Services\DocumentChecklistService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChecklistManager extends Component
{
    use WithFileUploads;

    public Service $service;
    public ?DocumentChecklist $checklist = null;

    // Reject Modal State
    public bool $showRejectModal = false;
    public ?int $rejectingItemId = null;
    public ?int $rejectingDocumentId = null;
    public string $rejectionReason = '';

    // History Modal State
    public bool $showHistoryModal = false;
    public ?int $historyItemId = null;

    // Direct Upload Modal State
    public bool $showUploadModal = false;
    public ?int $uploadingItemId = null;
    public $uploadFile;

    // Document Request Modal State
    public bool $showRequestModal = false;
    public array $selectedRequestItems = [];
    public string $requestMessage = '';
    public string $requestSubject = '';

    protected $listeners = ['refreshChecklist' => '$refresh'];

    public function mount(Service $service)
    {
        if ($service->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $this->service = $service;
        $this->loadChecklist();
    }

    public function loadChecklist(): void
    {
        $checklistService = new DocumentChecklistService();
        $this->checklist = $checklistService->generateForService($this->service);
    }

    public function verifyItem(int $itemId): void
    {
        $tenantId = Auth::user()->tenant_id;
        $item = DocumentChecklistItem::where('tenant_id', $tenantId)->findOrFail($itemId);

        $item->status = DocumentStatus::VERIFIED->value;
        $item->save();

        if ($item->current_document_id) {
            $doc = Document::where('tenant_id', $tenantId)->find($item->current_document_id);
            if ($doc) {
                $doc->update([
                    'status' => DocumentStatus::VERIFIED->value,
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
            }
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->service->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Document Verified',
            'description' => "Verified checklist item '{$item->name}' for {$this->service->type->label()}",
        ]);

        session()->flash('success', "Item '{$item->name}' marked as Verified.");
        $this->loadChecklist();
    }

    public function openRejectModal(int $itemId): void
    {
        $this->rejectingItemId = $itemId;
        $item = DocumentChecklistItem::where('tenant_id', Auth::user()->tenant_id)->findOrFail($itemId);
        $this->rejectingDocumentId = $item->current_document_id;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:500',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $item = DocumentChecklistItem::where('tenant_id', $tenantId)->findOrFail($this->rejectingItemId);

        $item->status = DocumentStatus::REJECTED->value;
        $item->save();

        if ($this->rejectingDocumentId) {
            $doc = Document::where('tenant_id', $tenantId)->find($this->rejectingDocumentId);
            if ($doc) {
                $doc->update([
                    'status' => DocumentStatus::REJECTED->value,
                    'rejection_reason' => $this->rejectionReason,
                ]);
            }
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->service->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Document Rejected',
            'description' => "Rejected document for '{$item->name}'. Reason: {$this->rejectionReason}",
        ]);

        session()->flash('success', "Document for '{$item->name}' marked as Rejected.");
        $this->showRejectModal = false;
        $this->loadChecklist();
    }

    public function openUploadModal(int $itemId): void
    {
        $this->uploadingItemId = $itemId;
        $this->uploadFile = null;
        $this->resetValidation();
        $this->showUploadModal = true;
    }

    public function uploadReplacement(): void
    {
        $this->validate([
            'uploadFile' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $item = DocumentChecklistItem::where('tenant_id', $tenantId)->findOrFail($this->uploadingItemId);

        $directory = 'tenants/' . $tenantId . '/clients/' . $this->service->client_id . '/documents';
        $path = $this->uploadFile->store($directory, 'private');
        $originalFilename = $this->uploadFile->getClientOriginalName();
        $mimeType = $this->uploadFile->getClientMimeType();
        $fileSize = $this->uploadFile->getSize();

        $previousDoc = $item->current_document_id ? Document::where('tenant_id', $tenantId)->find($item->current_document_id) : null;

        $newDoc = Document::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->service->client_id,
            'service_id' => $this->service->id,
            'checklist_item_id' => $item->id,
            'name' => $item->name,
            'file_path' => $path,
            'category' => $item->document_type ?? 'Checklist Document',
            'document_type' => $item->document_type ?? 'Checklist Document',
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => DocumentStatus::RECEIVED->value,
            'uploaded_by' => Auth::id(),
            'is_current' => true,
        ]);

        if ($previousDoc) {
            $previousDoc->update([
                'is_current' => false,
                'replaced_by_id' => $newDoc->id,
            ]);
        }

        $item->update([
            'status' => DocumentStatus::RECEIVED->value,
            'current_document_id' => $newDoc->id,
        ]);

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->service->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Document Replaced',
            'description' => "Uploaded new replacement file for '{$item->name}'",
        ]);

        session()->flash('success', "New document uploaded for '{$item->name}'.");
        $this->showUploadModal = false;
        $this->loadChecklist();
    }

    public function openHistoryModal(int $itemId): void
    {
        $this->historyItemId = $itemId;
        $this->showHistoryModal = true;
    }

    public function openRequestModal(): void
    {
        if (!$this->checklist) {
            return;
        }

        // Auto select pending & rejected items
        $this->selectedRequestItems = $this->checklist->items
            ->filter(fn($i) => in_array($i->status->value, ['pending', 'rejected']))
            ->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();

        $this->updateRequestPreview();
        $this->showRequestModal = true;
    }

    public function updateRequestPreview(): void
    {
        $selectedIds = array_filter($this->selectedRequestItems);
        $items = DocumentChecklistItem::whereIn('id', $selectedIds)->get();

        $clientName = $this->service->client->name;
        $serviceType = strtoupper($this->service->type->value);
        $fyLabel = $this->service->financialYear ? $this->service->financialYear->year_label : '';

        $this->requestSubject = "Document Request: {$serviceType} ({$fyLabel}) - {$clientName}";

        $itemListStr = "";
        $index = 1;
        foreach ($items as $item) {
            $itemListStr .= "{$index}. {$item->name}\n";
            $index++;
        }

        $this->requestMessage = "Hello {$clientName},\n\nPlease submit the following pending documents for your {$serviceType} for {$fyLabel}:\n\n{$itemListStr}\nThank you,\n" . Auth::user()->name;
    }

    public function createDocumentRequest(string $status = 'sent'): void
    {
        $tenantId = Auth::user()->tenant_id;
        $selectedIds = array_filter($this->selectedRequestItems);

        if (empty($selectedIds)) {
            session()->flash('error', 'Please select at least one document to request.');
            return;
        }

        // Prevent duplicate active requests for the exact same client + service + FY
        $activeRequest = \App\Models\DocumentRequest::where('tenant_id', $tenantId)
            ->where('client_id', $this->service->client_id)
            ->where('service_id', $this->service->id)
            ->whereIn('status', [\App\Enums\DocumentRequestStatus::DRAFT->value, \App\Enums\DocumentRequestStatus::SENT->value])
            ->first();

        if ($activeRequest && $status === 'sent') {
            // Update existing active request
            $activeRequest->update([
                'message' => $this->requestMessage,
                'subject' => $this->requestSubject,
                'sent_at' => now(),
                'status' => \App\Enums\DocumentRequestStatus::SENT->value,
            ]);
            $req = $activeRequest;
        } else {
            $token = \Illuminate\Support\Str::random(32);
            $req = \App\Models\DocumentRequest::create([
                'tenant_id' => $tenantId,
                'client_id' => $this->service->client_id,
                'service_id' => $this->service->id,
                'financial_year_id' => $this->service->financial_year_id,
                'created_by' => Auth::id(),
                'status' => $status,
                'message' => $this->requestMessage,
                'subject' => $this->requestSubject,
                'sent_at' => $status === 'sent' ? now() : null,
                'expires_at' => now()->addDays(14),
                'upload_token' => $token,
                'token_expires_at' => now()->addDays(14),
            ]);

            foreach ($selectedIds as $itemId) {
                $item = DocumentChecklistItem::find($itemId);
                if ($item) {
                    \App\Models\DocumentRequestItem::create([
                        'document_request_id' => $req->id,
                        'checklist_item_id' => $item->id,
                        'item_name' => $item->name,
                    ]);
                }
            }
        }

        // Dispatch WhatsApp message if sent
        if ($status === 'sent') {
            try {
                $wsService = new \App\Services\Communication\WhatsApp\WhatsAppService();
                $uploadUrl = route('client.upload.portal', $req->upload_token);
                $fullMessage = $this->requestMessage . "\n\nSecure Upload Portal: " . $uploadUrl;
                $wsService->sendText($this->service->client, $fullMessage);
            } catch (\Exception $e) {
                session()->flash('warning', 'Document request saved, but WhatsApp dispatch was skipped: ' . $e->getMessage());
            }
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->service->client_id,
            'user_id' => Auth::id(),
            'event_type' => 'Document Requested',
            'description' => "Created document request for {$this->service->type->label()} (" . count($selectedIds) . " items requested)",
        ]);

        session()->flash('success', 'Document request saved/sent successfully.');
        $this->showRequestModal = false;
        $this->loadChecklist();
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $historyItem = $this->historyItemId
            ? DocumentChecklistItem::where('tenant_id', $tenantId)->with(['documents' => function ($q) {
                $q->with(['uploader', 'verifier'])->latest();
            }])->find($this->historyItemId)
            : null;

        return view('livewire.documents.checklist-manager', [
            'metrics' => $this->checklist ? $this->checklist->metrics : null,
            'historyItem' => $historyItem,
        ]);
    }
}
