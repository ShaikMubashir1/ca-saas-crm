<?php

namespace App\Livewire\Credentials;

use App\Models\Client;
use App\Models\ClientCredential;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Client $client;
    public ClientCredential $credential;

    public string $portal_name = '';
    public string $username = '';
    public string $password = '';
    public string $notes = '';

    public array $portalOptions = [
        'GST Portal',
        'Income Tax Portal',
        'MCA Portal',
        'TRACES',
        'EPFO',
        'ESIC',
        'Professional Tax',
        'Bank Login',
        'Other'
    ];

    protected function rules(): array
    {
        return [
            'portal_name' => 'required|in:' . implode(',', $this->portalOptions),
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    public function mount(Client $client, ClientCredential $credential)
    {
        if ($client->tenant_id !== Auth::user()->tenant_id || $credential->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access.');
        }

        if ($credential->client_id !== $client->id) {
            abort(404);
        }

        $this->client = $client;
        $this->credential = $credential;
        
        $this->portal_name = $credential->portal_name;
        $this->username = $credential->username;
        $this->password = $credential->password; // decrypts automatically via Eloquent cast
        $this->notes = $credential->notes ?? '';
    }

    public function save()
    {
        $this->validate();

        $this->credential->update([
            'portal_name' => $this->portal_name,
            'username' => $this->username,
            'password' => $this->password,
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('success', 'Credential updated successfully.');

        return $this->redirect(route('clients.show', $this->client->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.credentials.edit');
    }
}
