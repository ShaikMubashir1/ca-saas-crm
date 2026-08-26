<?php

namespace App\Livewire\Credentials;

use App\Models\Client;
use App\Models\ClientCredential;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public Client $client;

    public string $portal_name = 'GST Portal';
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

    public function mount(Client $client)
    {
        if ($client->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this client.');
        }
        $this->client = $client;
    }

    public function save()
    {
        $this->validate();

        ClientCredential::create([
            'tenant_id' => Auth::user()->tenant_id,
            'client_id' => $this->client->id,
            'portal_name' => $this->portal_name,
            'username' => $this->username,
            'password' => $this->password, // automatically encrypted by model cast
            'notes' => $this->notes ?: null,
        ]);

        session()->flash('success', 'Credential added successfully.');

        return $this->redirect(route('clients.show', $this->client->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.credentials.create');
    }
}
