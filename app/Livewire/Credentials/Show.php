<?php

namespace App\Livewire\Credentials;

use App\Models\Client;
use App\Models\ClientCredential;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public Client $client;
    public ClientCredential $credential;

    public bool $showPassword = false;

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
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('livewire.credentials.show');
    }
}
