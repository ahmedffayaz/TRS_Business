<?php

namespace App\Livewire\Backend\Comment;

use App\Models\Task;
use App\Models\Comment;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatComponent extends Component
{
    public ?int $taskId;

    public function mount($taskId)
    {
        $this->taskId = $taskId;
    }

    private function getComments()
    {
        return Comment::where('task_id', $this->taskId)->whereHas('task', function ($query) {
            $query->whereHas('project', function ($query) {
                $query->sessionBusiness();
            });
        })->with('fromUser')->get();
    }

    private function getTask()
    {
        return Task::whereHas('project', function ($query) {
            $query->sessionBusiness();
        })->with(['project', 'comments'])->findOrFail($this->taskId);
    }

    public function render()
    {
        $comments = $this->getComments();
        $task = $this->getTask();
        $this->dispatch('reinitialize-icons');

        return view('livewire.backend.comment.chat-component', compact('comments', 'task'));
    }
}
