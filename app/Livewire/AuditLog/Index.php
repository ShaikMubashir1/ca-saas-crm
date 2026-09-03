<?php

namespace App\Livewire\AuditLog;

use App\Models\TimelineEvent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $tenantId = Auth::user()->tenant_id;

        $events = TimelineEvent::where('tenant_id', $tenantId)
            ->with(['client', 'user'])
            ->when($this->search, function ($q) {
                $q->where('event_type', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$this->search}%"));
            })
            ->latest()
            ->paginate(15);

        return view('livewire.audit-log.index', [
            'events' => $events,
        ]);
    }
}
