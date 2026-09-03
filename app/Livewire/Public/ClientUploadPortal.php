<?php

namespace App\Livewire\Public;

use App\Models\DocumentRequest;
use App\Models\DocumentChecklistItem;
use App\Models\Document;
use App\Models\TimelineEvent;
use App\Enums\DocumentStatus;
use App\Enums\DocumentRequestStatus;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class ClientUploadPortal extends Component
{
    use WithFileUploads;

    public string $token = '';
    public ?DocumentRequest $requestRecord = null;
    public ?int $selectedItemId = null;
    public $uploadFile;

    public function mount(string $token): void
    {
        $this->token = $token;

        // 1. First check hashed ClientPortalToken
        $hash = hash('sha256', $token);
        $portalTokenRecord = \App\Models\ClientPortalToken::withoutGlobalScopes()
            ->where('token_hash', $hash)
            ->first();

        if ($portalTokenRecord) {
            if ($portalTokenRecord->isRevoked()) {
                abort(404, 'Invalid or revoked upload portal link.');
            }
            if ($portalTokenRecord->isExpired()) {
                abort(410, 'This upload request link has expired.');
            }
            $portalTokenRecord->update(['last_used_at' => now()]);
            $this->requestRecord = $portalTokenRecord->documentRequest()
                ->with(['client', 'service.financialYear', 'items.checklistItem'])
                ->first();
        } else {
            // 2. Fallback to direct request token
            $this->requestRecord = DocumentRequest::where('upload_token', $token)
                ->with(['client', 'service.financialYear', 'items.checklistItem'])
                ->first();
        }

        if (!$this->requestRecord) {
            abort(404, 'Invalid or expired upload portal link.');
        }

        if ($this->requestRecord->token_expires_at && $this->requestRecord->token_expires_at->isPast()) {
            abort(410, 'This upload request link has expired.');
        }
    }

    public function uploadDocument(): void
    {
        $this->validate([
            'selectedItemId' => 'required|integer',
            'uploadFile' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
        ]);

        $item = DocumentChecklistItem::findOrFail($this->selectedItemId);
        $tenantId = $this->requestRecord->tenant_id;
        $clientId = $this->requestRecord->client_id;

        $directory = 'tenants/' . $tenantId . '/clients/' . $clientId . '/documents';
        $path = $this->uploadFile->store($directory, 'private');
        $originalFilename = $this->uploadFile->getClientOriginalName();
        $mimeType = $this->uploadFile->getClientMimeType();
        $fileSize = $this->uploadFile->getSize();

        $previousDoc = $item->current_document_id ? Document::where('tenant_id', $tenantId)->find($item->current_document_id) : null;

        $newDoc = Document::create([
            'tenant_id' => $tenantId,
            'client_id' => $clientId,
            'service_id' => $this->requestRecord->service_id,
            'checklist_item_id' => $item->id,
            'name' => $item->name,
            'file_path' => $path,
            'category' => $item->document_type ?? 'Checklist Document',
            'document_type' => $item->document_type ?? 'Checklist Document',
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => DocumentStatus::RECEIVED->value,
            'uploaded_by' => null, // Uploaded by Client
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

        // Mark request item received
        $reqItem = $this->requestRecord->items->where('checklist_item_id', $item->id)->first();
        if ($reqItem) {
            $reqItem->update(['received_at' => now()]);
        }

        // Check if all request items received
        $remainingCount = $this->requestRecord->items->whereNull('received_at')->count();
        if ($remainingCount === 0) {
            $this->requestRecord->update(['status' => DocumentRequestStatus::COMPLETED->value]);
        } else {
            $this->requestRecord->update(['status' => DocumentRequestStatus::PARTIALLY_RECEIVED->value]);
        }

        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $clientId,
            'user_id' => null,
            'event_type' => 'Client Upload Received',
            'description' => "Client uploaded document '{$item->name}' via secure portal link",
        ]);

        session()->flash('success', "File for '{$item->name}' uploaded successfully. Thank you!");
        $this->uploadFile = null;
        $this->selectedItemId = null;
        $this->requestRecord->refresh();
    }

    public function render()
    {
        return view('livewire.public.client-upload-portal');
    }
}

