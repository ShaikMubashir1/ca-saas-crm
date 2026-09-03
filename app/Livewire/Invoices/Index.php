<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\Client;
use App\Enums\InvoiceStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $selectedStatus = '';
    public ?int $selectedClient = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        // Auto update overdue invoices on list render
        Invoice::where('tenant_id', $tenantId)
            ->where('status', '!=', InvoiceStatus::PAID->value)
            ->where('status', '!=', InvoiceStatus::CANCELLED->value)
            ->where('due_date', '<', date('Y-m-d'))
            ->where('balance_due', '>', 0)
            ->update(['status' => InvoiceStatus::OVERDUE->value]);

        $invoices = Invoice::where('tenant_id', $tenantId)
            ->with(['client', 'creator'])
            ->when($this->search, fn($q) => $q->where('invoice_number', 'like', "%{$this->search}%")->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$this->search}%")))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->selectedClient, fn($q) => $q->where('client_id', $this->selectedClient))
            ->orderBy('id', 'desc')
            ->paginate(12);

        $clients = Client::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('livewire.invoices.index', [
            'invoices' => $invoices,
            'clients' => $clients,
        ]);
    }
}
