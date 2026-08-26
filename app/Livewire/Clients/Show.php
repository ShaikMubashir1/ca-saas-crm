<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use App\Models\Document;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Show extends Component
{
    public Client $client;

    public function mount(Client $client)
    {
        // Security check: Ensure client belongs to authenticated user's tenant
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this client.');
        }
        $this->client = $client;
    }

    public function downloadDocument($documentId)
    {
        // BelongsToTenant automatically scopes this, but double check manually for defense-in-depth
        $document = Document::findOrFail($documentId);

        if ($document->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this document.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            session()->flash('error', 'File not found on storage.');
            return;
        }

        return Storage::disk('public')->download($document->file_path, $document->name);
    }

    public function deleteDocument($documentId)
    {
        $document = Document::findOrFail($documentId);

        if ($document->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this document.');
        }

        // Delete from physical storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Delete record
        $document->delete();

        session()->flash('success', 'Document deleted successfully.');
    }

    public function deleteCredential($credentialId)
    {
        $credential = \App\Models\ClientCredential::findOrFail($credentialId);

        if ($credential->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this credential.');
        }

        $credential->delete();

        session()->flash('success', 'Credential deleted successfully.');
    }

    public function render()
    {
        return view('livewire.clients.show', [
            'documents' => $this->client->documents()->latest()->get(),
            'credentials' => $this->client->credentials()->latest()->get(),
            'tasks' => $this->client->tasks()->latest()->get(),
        ]);
    }
}
