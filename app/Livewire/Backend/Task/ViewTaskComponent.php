<?php

namespace App\Livewire\Backend\Task;

use App\Models\Task;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Task Details')]
class ViewTaskComponent extends Component
{
    public int $id;

    public function mount($id)
    {
        $this->id = $id;
    }

    private function getTask()
    {
        return Task::whereHas('project', function ($query) {
            $query->sessionBusiness();
        })->with(['project', 'comments'])->findOrFail($this->id);
    }

    public function render()
    {
        $task = $this->getTask();
        return view('livewire.backend.task.view-task-component', compact('task'));
    }
}
