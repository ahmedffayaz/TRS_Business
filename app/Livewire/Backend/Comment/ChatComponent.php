<?php

namespace App\Livewire\Backend\Comment;

use App\Livewire\Forms\ChatHourForm;
use App\Models\Task;
use App\Models\Comment;
use Livewire\Component;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatComponent extends Component
{
    public ?int $taskId;
    public $isHourModalVisible = false;

    public ChatHourForm $form;

    public int $limitPerPage = 10;

    public string $sortDirection = 'desc';

    public string $columnName = 'created_at';

    public $hideShowMoreButton = true;
    public function mount($taskId)
    {
        $this->taskId = $taskId;
    }

    private function getComments() : LengthAwarePaginator
    {
        $comments = Comment::where('task_id', $this->taskId)
        ->whereHas('task', function ($query) {
            $query->whereHas('project', function ($query) {
                $query->sessionBusiness();
            });
        })
        ->orderBy('id', $this->sortDirection)
        ->with('fromUser')
        ->paginate($this->limitPerPage);

            $hide = !$comments->hasMorePages();
            $this->hideShowMoreButton = $hide ? false : true;

            return $comments;
    }
    public function loadMore()
    {
        $this->limitPerPage += 10;
        $this->getComments();
    }
    public function showElement()
    {
        $this->isHourModalVisible = true;
        $this->form->isHourModalVisible = true;
        $this->dispatch('reinitialize-dispatcher');
    }

    public function hideElement()
    {
        $this->isHourModalVisible = false;
        $this->form->isHourModalVisible = false;
        $this->form->dated = null;
        $this->form->time = null;
        $this->form->unit = null;
        $this->form->is_billable = null;
;
        $this->resetValidation();
        $this->dispatch('reinitialize-dispatcher');
    }
    public function storeChatHours()
    {
        if (!$this->isHourModalVisible && empty($this->form->description)) {
            return $this->dispatch('alert', ['type' => 'error', 'message' => 'Description field is required.']);
        }
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            Comment::create([
                'description' => $validated['description'],
                'time' => $validated['time'] ?? null,
                'type' => 'comment',
                'task_id' => $this->taskId,
                'from' => auth()->user()->id,
                'dated' => $validated['dated'] ?? null,
                'is_billable' => $this->validated['is_billable'] ?? 0,
            ]);

            DB::commit();
            $this->form->description ="";
            $this->dispatch('alert', ['type' => 'success', 'message' => 'comment created successfully.']);
            $this->form->reset();
            $this->resetValidation();
            $this->form->isHourModalVisible = false;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
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
