<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $priority = '';
    public string $serviceType = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'priority' => ['except' => ''],
        'serviceType' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingPriority(): void { $this->resetPage(); }
    public function updatingServiceType(): void { $this->resetPage(); }

    public function markComplete($taskId)
    {
        $task = Task::findOrFail($taskId);
        if ($task->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        session()->flash('success', 'Task marked as completed.');
    }

    public function deleteTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        if ($task->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
        $task->delete();
        session()->flash('success', 'Task deleted successfully.');
    }

    public function render()
    {
        $query = Task::with(['client', 'assignee']);

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', function ($cq) {
                      $cq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->status)) {
            if ($this->status === 'overdue') {
                $query->where('due_date', '<', now()->toDateString())
                      ->where('status', '!=', 'completed');
            } else {
                $query->where('status', $this->status);
            }
        }

        if (!empty($this->priority)) {
            $query->where('priority', $this->priority);
        }

        if (!empty($this->serviceType)) {
            $query->where('service_type', $this->serviceType);
        }

        // Dashboard widget counts
        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $dueToday = Task::where('due_date', now()->toDateString())
                        ->where('status', '!=', 'completed')->count();
        $overdueTasks = Task::where('due_date', '<', now()->toDateString())
                            ->where('status', '!=', 'completed')->count();
        $completedThisMonth = Task::where('status', 'completed')
                                  ->whereMonth('completed_at', now()->month)
                                  ->whereYear('completed_at', now()->year)
                                  ->count();

        return view('livewire.tasks.index', [
            'tasks' => $query->latest()->paginate(15),
            'totalTasks' => $totalTasks,
            'pendingTasks' => $pendingTasks,
            'dueToday' => $dueToday,
            'overdueTasks' => $overdueTasks,
            'completedThisMonth' => $completedThisMonth,
        ]);
    }
}
