<?php

namespace App\Livewire\Backend\Comment;

use App\Livewire\Forms\ChatHourForm;
use App\Models\Task;
use App\Models\Comment;
use App\Models\User;
use Livewire\Component;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
class ChatComponent extends Component
{
    public ?int $taskId;
    public $isHourModalVisible = false;

    public ChatHourForm $form;

    public int $limitPerPage = 10;

    public string $sortDirection = 'desc';

    public string $columnName = 'created_at';

    public bool $hideShowMoreButton = true;

    public string $filterBy = "";
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
        })->orderBy('id', $this->sortDirection)->with('fromUser');

        if ($this->filterBy === 'chatsOnly')
            $comments = $comments->whereNull('time')->whereNull('dated');
        if ($this->filterBy === 'hoursOnly')
            $comments = $comments->whereNotNull('time')->whereNotNull('dated');

        $comments = $comments->paginate($this->limitPerPage);

        $this->hideShowMoreButton = !$comments->hasMorePages() ? false : true;

        return $comments;
    }
    private function getMembers()
    {
        return User::sessionBusiness()->join('comments', function ($join) {
            $join->on('users.id', '=', 'comments.from')->orOn('users.id', '=', 'comments.to');
        })->groupBy('users.id')
            ->select('users.*')->get();
    }

    #[On('chats')]
    public function chats()
    {
        $this->getComments();
    }
    public function loadMore()
    {
        $this->limitPerPage += $this->perPageLimit();
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

    public function filterByWithoutHours()
    {
        $this->limitPerPage = $this->perPageLimit();
        $this->filterBy = "chatsOnly";
        return $this->getComments();
    }

    public function filterByHours()
    {
        $this->limitPerPage = $this->perPageLimit();
        $this->filterBy = "hoursOnly";
        return $this->getComments();
    }

    public function showAll()
    {
        $this->limitPerPage = $this->perPageLimit();
        $this->filterBy = "";
        $this->getComments();
    }

    public function storeChatHours()
    {
        if (!$this->isHourModalVisible && empty($this->form->description)) {
            return $this->dispatch('alert', ['type' => 'error', 'message' => 'Description field is required.']);
        }
        $validated = $this->form->validate();

        if (empty($validated['description'])) {
            return $this->dispatch('alert', ['type' => 'error', 'message' => 'Description field is required.']);
        }

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
            $this->form->reset();
            $this->resetValidation();
            $this->dispatch('comments');
            $this->dispatch('alert', ['type' => 'success', 'message' => 'comment created successfully.']);
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
        $members = $this->getMembers();
        $task = $this->getTask();
        $this->dispatch('reinitialize-icons');

        return view('livewire.backend.comment.chat-component', compact('comments', 'task', 'members'));
    }

    private function perPageLimit() : int
    {
        return 10;
    }
}
