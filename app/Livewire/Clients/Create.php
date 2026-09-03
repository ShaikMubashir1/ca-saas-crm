<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    public string $name = '';
    public string $entity_type = 'Individual';
    public string $client_type = 'individual';
    public string $email = '';
    public string $phone = '';
    public string $pan = '';
    public string $aadhaar = '';
    public string $gstin = '';
    public string $tan = '';
    public string $cin = '';
    public string $address = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'entity_type' => 'required|in:Individual,Proprietorship,Partnership Firm,LLP,Private Limited Company,Public Limited Company,HUF,Trust',
            'client_type' => 'required|string|in:individual,salaried,proprietor,partnership_firm,company,gst_business,tds_deductor,audit_client',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'pan' => ['nullable', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i'],
            'aadhaar' => ['nullable', 'string', 'regex:/^[0-9]{12}$/'],
            'gstin' => ['nullable', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i'],
            'tan' => ['nullable', 'string', 'regex:/^[A-Z]{4}[0-9]{5}[A-Z]{1}$/i'],
            'cin' => ['nullable', 'string', 'regex:/^[U|L][0-9]{5}[A-Z]{2}[0-9]{4}[A-Z]{3}[0-9]{6}$/i'],
            'address' => 'nullable|string',
        ];
    }

    public function store()
    {
        $validated = $this->validate();

        // Convert identifier values to uppercase for database uniformity
        $validated['pan'] = !empty($validated['pan']) ? strtoupper($validated['pan']) : null;
        $validated['gstin'] = !empty($validated['gstin']) ? strtoupper($validated['gstin']) : null;
        $validated['tan'] = !empty($validated['tan']) ? strtoupper($validated['tan']) : null;
        $validated['cin'] = !empty($validated['cin']) ? strtoupper($validated['cin']) : null;

        // Explicitly set tenant_id from authenticated user
        $validated['tenant_id'] = auth()->user()->tenant_id;

        Client::create($validated);

        session()->flash('success', 'Client created successfully.');

        return $this->redirect(route('clients.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.clients.create');
    }
}
