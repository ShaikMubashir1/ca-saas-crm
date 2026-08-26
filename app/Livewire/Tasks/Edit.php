<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    public Task $task;

    public string $client_id = '';
    public string $title = '';
    public string $description = '';
    public string $service_type = '';
    public string $priority = '';
    public string $status = '';
    public ?string $due_date = null;
    public string $assigned_to = '';

    public array $serviceTypes = [
        'GST Return',
        'TDS Return',
        'Income Tax Return',
        'ROC Filing',
        'Audit',
        'DSC Renewal',
        'PF / ESI',
        'Professional Tax',
        'Other',
    ];

    public array $priorities = ['low', 'medium', 'high', 'urgent'];
    public array $statuses = ['pending', 'in_progress', 'completed'];

    protected function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:' . implode(',', $this->serviceTypes),
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    public function mount(Task $task)
    {
        if ($task->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $this->task = $task;
        $this->client_id = (string) $task->client_id;
        $this->title = $task->title;
        $this->description = $task->description ?? '';
        $this->service_type = $task->service_type;
        $this->priority = $task->priority;
        $this->status = $task->status;
        $this->due_date = $task->due_date?->format('Y-m-d');
        $this->assigned_to = (string) ($task->assigned_to ?? '');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'client_id' => $this->client_id,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'service_type' => $this->service_type,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date ?: null,
            'assigned_to' => $this->assigned_to ?: null,
        ];

        // Auto-set completed_at when marking completed, clear when reopening
        if ($this->status === 'completed' && $this->task->status !== 'completed') {
            $data['completed_at'] = now();
        } elseif ($this->status !== 'completed' && $this->task->status === 'completed') {
            $data['completed_at'] = null;
        }

        $this->task->update($data);

        session()->flash('success', 'Task updated successfully.');

        return $this->redirect(route('tasks.show', $this->task->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.tasks.edit', [
            'clients' => Client::orderBy('name')->get(),
            'users' => User::where('tenant_id', Auth::user()->tenant_id)->orderBy('name')->get(),
        ]);
    }
}
