<?php
namespace App\Http\Livewire\Documents;

use App\Models\Document;
use App\Models\Client;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Upload extends Component
{
    use WithFileUploads, WithPagination;

    public $clientId;
    public $name;
    public $category = 'General';
    public $document; // UploadedFile instance

    protected $rules = [
        'clientId' => 'required|exists:clients,id',
        'name' => 'required|string|max:255',
        'category' => 'required|string|max:100',
        'document' => 'required|file|max:10240', // 10 MB max
    ];

    public function mount($clientId)
    {
        $this->clientId = $clientId;
    }

    public function submit()
    {
        $this->validate();

        $client = Client::findOrFail($this->clientId);

        // Ensure tenant isolation
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $path = $this->document->store('documents', 'private'); // stores in storage/app/private/documents

        Document::create([
            'tenant_id' => Auth::user()->tenant_id,
            'client_id' => $client->id,
            'name' => $this->name,
            'file_path' => $path,
            'category' => $this->category,
            'uploaded_by' => Auth::id(),
        ]);

        $this->reset(['name', 'category', 'document']);
        $this->emit('documentUploaded');
        session()->flash('message', 'Document uploaded successfully.');
    }

    public function render()
    {
        return view('livewire.documents.upload');
    }
}
