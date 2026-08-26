<?php

namespace App\Livewire\Documents;

use App\Models\Client;
use App\Models\Document;
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
        'DSC',
        'Agreement',
        'Other'
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'document_type' => 'required|in:' . implode(',', $this->documentTypes),
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,xlsx,docx|max:10240', // 10MB max
        ];
    }

    public function mount(Client $client)
    {
        // Tenant security check
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }
        $this->client = $client;
    }

    public function save()
    {
        $this->validate();

        // Target directory: storage/app/public/clients/{client_id}/documents
        // Relative path within public disk: clients/{client_id}/documents
        $directory = 'clients/' . $this->client->id . '/documents';
        
        $path = $this->file->store($directory, 'public');

        Document::create([
            'tenant_id' => Auth::user()->tenant_id,
            'client_id' => $this->client->id,
            'name' => $this->title,          // DB Column: name
            'file_path' => $path,           // DB Column: file_path
            'category' => $this->document_type, // DB Column: category
            'uploaded_by' => Auth::id(),    // DB Column: uploaded_by
        ]);

        session()->flash('success', 'Document uploaded successfully.');

        return $this->redirect(route('clients.show', $this->client->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.documents.upload');
    }
}
