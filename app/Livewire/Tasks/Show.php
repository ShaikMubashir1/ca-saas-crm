<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public Task $task;

    public function mount(Task $task)
    {
        if ($task->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
        $this->task = $task;
    }

    public function markComplete()
    {
        $this->task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        session()->flash('success', 'Task marked as completed.');
    }

    public function reopenTask()
    {
        $this->task->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);
        session()->flash('success', 'Task reopened.');
    }

    public function deleteTask()
    {
        $this->task->delete();
        session()->flash('success', 'Task deleted successfully.');
        return $this->redirect(route('tasks.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.tasks.show');
    }
}
