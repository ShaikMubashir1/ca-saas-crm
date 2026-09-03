<?php

namespace App\Livewire\Documents;

use App\Models\Document;
use App\Models\Client;
use App\Models\Service;
use App\Models\FinancialYear;
use App\Enums\DocumentStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DocumentsPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = '';
    public string $selectedClient = '';
    public string $selectedFY = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedClient(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedFY(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $documents = Document::where('tenant_id', $tenantId)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->selectedClient, fn($q) => $q->where('client_id', $this->selectedClient))
            ->when($this->selectedFY, fn($q) => $q->whereHas('service', fn($sq) => $sq->where('financial_year_id', $this->selectedFY)))
            ->with(['client', 'service.financialYear', 'uploader', 'verifier'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $clients = Client::where('tenant_id', $tenantId)->orderBy('name')->get();
        $financialYears = FinancialYear::where('tenant_id', $tenantId)->orderBy('year_label', 'desc')->get();

        return view('livewire.documents.documents-page', [
            'documents' => $documents,
            'clients' => $clients,
            'financialYears' => $financialYears,
        ]);
    }
}

