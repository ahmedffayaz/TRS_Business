<?php

namespace App\Livewire\Backend\Task;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Tasks')]
class TaskComponent extends Component
{
    public function render()
    {
        return view('livewire.backend.task.task-component');
    }
}
