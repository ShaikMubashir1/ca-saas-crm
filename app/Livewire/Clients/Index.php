<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $entityType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'entityType' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEntityType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Client::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('gstin', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->entityType)) {
            $query->where('entity_type', $this->entityType);
        }

        $clients = $query->paginate(10);

        return view('livewire.clients.index', [
            'clients' => $clients,
        ]);
    }
}
