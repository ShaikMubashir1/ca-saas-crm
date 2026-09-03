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
    public string $clientType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'entityType' => ['except' => ''],
        'clientType' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingEntityType(): void
    {
        $this->resetPage();
    }

    public function updatingClientType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Client::query()
            ->withCount([
                'services as active_services_count' => function ($q) {
                    $q->whereNotIn('status', ['completed', 'filed', 'acknowledged']);
                }
            ])
            ->with(['services' => function ($q) {
                $q->latest()->limit(3);
            }, 'timelineEvents' => function ($q) {
                $q->latest()->limit(1);
            }]);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('pan', 'like', '%' . $this->search . '%')
                  ->orWhere('gstin', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->entityType)) {
            $query->where('entity_type', $this->entityType);
        }

        if (!empty($this->clientType)) {
            $query->where('client_type', $this->clientType);
        }

        $clients = $query->paginate(15);

        return view('livewire.clients.index', [
            'clients' => $clients,
        ]);
    }
}

