<?php

namespace App\Livewire\Tasks;

use Livewire\Component;

class Create extends Component
{
    public $title;
    public $description;

    public function submit()
    {
        // Placeholder: normally you would persist the task.
        $this->emit('taskCreated');
        session()->flash('message', 'Task created successfully.');
    }

    public function render()
    {
        return view('livewire.tasks.create');
    }
}
