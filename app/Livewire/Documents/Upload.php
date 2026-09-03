<?php

namespace App\Livewire\Documents;

use App\Models\Client;
use App\Models\Document;
use App\Models\Service;
use App\Models\DocumentChecklistItem;
use App\Models\TimelineEvent;
use App\Enums\DocumentStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Upload extends Component
{
    use WithFileUploads;

    public Client $client;
    public ?int $service_id = null;
    public ?int $checklist_item_id = null;
    
    // Form properties
    public string $title = '';
    public string $document_type = 'PAN Card';
    public $file;

    public array $documentTypes = [
        'PAN Card',
        'Aadhaar Card',
        'GST Certificate',
        'ITR',
        'ROC',
        'Financial Statement',
        'Bank Statement',
        'Form 16',
        '80C Proof',
        'Rent Receipt',
        'Capital Gains',
        'Sales Register',
        'Purchase Register',
        'TDS Challan',
        'Payment Register',
        'Trial Balance',
        'Ledger',
        'Fixed Asset Register',
        'Agreement',
        'Other'
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'document_type' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240', // 10MB max
            'service_id' => 'nullable|integer',
            'checklist_item_id' => 'nullable|integer',
        ];
    }

    public function mount(Client $client)
    {
        // Tenant security check
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        $this->client = $client;
        $this->service_id = request()->query('service_id');
        $this->checklist_item_id = request()->query('checklist_item_id');

        if ($this->checklist_item_id) {
            $item = DocumentChecklistItem::where('tenant_id', Auth::user()->tenant_id)->find($this->checklist_item_id);
            if ($item) {
                $this->title = $item->name;
                $this->document_type = $item->document_type ?? 'Other';
            }
        }
    }

    public function save()
    {
        $this->validate();

        $tenantId = Auth::user()->tenant_id;
        $directory = 'tenants/' . $tenantId . '/clients/' . $this->client->id . '/documents';
        
        // Store in private non-public disk
        $path = $this->file->store($directory, 'private');
        $originalFilename = $this->file->getClientOriginalName();
        $mimeType = $this->file->getClientMimeType();
        $fileSize = $this->file->getSize();

        // Check replacement logic if this checklist item already has a document
        $previousDoc = null;
        if ($this->checklist_item_id) {
            $checkItem = DocumentChecklistItem::where('tenant_id', $tenantId)->find($this->checklist_item_id);
            if ($checkItem && $checkItem->current_document_id) {
                $previousDoc = Document::where('tenant_id', $tenantId)->find($checkItem->current_document_id);
            }
        }

        // Create new document record
        $document = Document::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->client->id,
            'service_id' => $this->service_id,
            'checklist_item_id' => $this->checklist_item_id,
            'name' => $this->title,
            'file_path' => $path,
            'category' => $this->document_type,
            'document_type' => $this->document_type,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => DocumentStatus::RECEIVED->value,
            'uploaded_by' => Auth::id(),
            'is_current' => true,
        ]);

        // Link previous document if replacing
        if ($previousDoc) {
            $previousDoc->update([
                'is_current' => false,
                'replaced_by_id' => $document->id,
            ]);
        }

        // Link to checklist item
        if ($this->checklist_item_id) {
            $checkItem = DocumentChecklistItem::where('tenant_id', $tenantId)->find($this->checklist_item_id);
            if ($checkItem) {
                $checkItem->update([
                    'status' => DocumentStatus::RECEIVED->value,
                    'current_document_id' => $document->id,
                ]);
            }
        }

        // Record timeline event
        TimelineEvent::create([
            'tenant_id' => $tenantId,
            'client_id' => $this->client->id,
            'user_id' => Auth::id(),
            'event_type' => 'Document Uploaded',
            'description' => "Uploaded document '{$this->title}' ({$this->document_type})",
        ]);

        session()->flash('success', 'Document uploaded successfully.');

        return $this->redirect(route('clients.show', $this->client->id), navigate: true);
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;
        $services = Service::where('tenant_id', $tenantId)->where('client_id', $this->client->id)->with('financialYear')->get();

        return view('livewire.documents.upload', [
            'services' => $services,
        ]);
    }
}
