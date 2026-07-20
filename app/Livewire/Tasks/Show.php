<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;

class Show extends Component
{
    public $taskId;
    public $task;

    public function mount($task)
    {
        $this->taskId = $task;
        $this->task = Task::findOrFail($task);
    }

    public function render()
    {
        return view('livewire.tasks.show');
    }
}
