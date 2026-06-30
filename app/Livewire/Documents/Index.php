<?php
namespace App\Livewire\Documents;

use App\Models\Document;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $clientId;
    public $search = '';

    protected $listeners = ['documentUploaded' => '$refresh'];

    public function mount($clientId)
    {
        $this->clientId = $clientId;
    }

    public function getDocumentsProperty()
    {
        return Document::where('client_id', $this->clientId)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function download($documentId)
    {
        $document = Document::findOrFail($documentId);
        // Ensure tenant isolation
        if ($document->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
        $url = Storage::disk('private')->temporaryUrl(
            $document->file_path,
            now()->addMinutes(30)
        );
        return redirect($url);
    }

    public function render()
    {
        return view('livewire.documents.index', [
            'documents' => $this->documents,
        ]);
    }
}
