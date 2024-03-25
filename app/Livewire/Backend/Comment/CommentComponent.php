<?php

namespace App\Livewire\Backend\Comment;

use Exception;
use App\Models\Comment;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Enums\Comment\CommentType;
use App\Enums\Comment\CommentUnit;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\CommentForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentComponent extends Component
{
    use WithMainModal, WithPagination;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 10;

    public ?int $taskId;

    public CommentForm $form;

    public function mount($taskId)
    {
        $this->taskId = $taskId;
    }

    private function getComments(): LengthAwarePaginator
    {
        return Comment::whereHas('task', function ($query) {
            $query->where('id', $this->taskId)->whereHas('project', function ($query) {
                $query->sessionBusiness();
            });
        })->with('fromUser')
            ->getList($this->search, $this->columnName, $this->sortDirection)->paginate($this->limitPerPage);
    }

    public function render()
    {
        $comments = $this->getComments();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.comment.comment-component', compact('comments'));
    }

    public function openModal()
    {
        $this->openMainModal();
    }

    public function closeModal()
    {
        $this->closeMainModal();
        $this->dispatch('unit-select', ['formUnit' => []]);
    }

    public function store()
    {
        $message = 'comment created successfully.';
        $type = CommentType::COMMENT->value;
        $validated = $this->form->validate();

        try {
            if ($validated['time']) {
                $type = CommentType::TIME->value;
            }

            if ($validated['unit'] === CommentUnit::HOURS->value) {
                $validated['time'] = $validated['time'] * 60;
            }

            DB::beginTransaction();
            $comment = Comment::create([
                'description' => $validated['description'],
                'time' => $validated['time'],
                'type' => $type,
                'task_id' => $this->taskId,
                'from' => auth()->user()->id,
                'dated' => $validated['dated'],
                'is_billable' => $validated['is_billable'] ? $validated['is_billable'] : false
            ]);

            $comment->task->project()->update(['last_updated_at' => now()]);

            DB::commit();

            // counting number of hours
            $time = Comment::where('from', auth()->user()->id)->whereDate('dated', $validated['dated'])->sum('time');

            if (convertMinutesToHours($time) > 8) {
                $message .= ' You have added more than 8 working hours';
            }

            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success', 'message' => $message]);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        try {
            $comment = comment::where('task_id', $this->taskId)->findOrFail($id);

            // Add unit
            $type = CommentUnit::MINUTES->value;
            if ($comment?->time >= 60 && $comment?->time % 60 === 0) {
                $type = CommentUnit::HOURS->value;
                $comment->time = convertMinutesToHours($comment?->time);
            }
            $comment->unit = $type;

            $this->form->set($comment);
            $this->dispatch('unit-select', ['formUnit' => $comment->unit]);

            $this->openModal();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while edit comment and comment id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit comment and comment id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $message = 'comment updated successfully.';
        $type = CommentType::COMMENT->value;
        $validated = $this->form->validate();

        try {
            $comment = comment::where('task_id', $this->taskId)->findOrFail($id);

            if ($validated['time']) {
                $type = CommentType::TIME->value;
            }

            if ($validated['unit'] === CommentUnit::HOURS->value) {
                $validated['time'] = $validated['time'] * 60;
            }

            DB::beginTransaction();
            $comment->update([
                'description' => $validated['description'],
                'time' => $validated['time'],
                'type' => $type,
                'task_id' => $this->taskId,
                'from' => auth()->user()->id,
                'dated' => $validated['dated'],
                'is_billable' => $validated['is_billable'] ? $validated['is_billable'] : false
            ]);

            $comment->task->project()->update(['last_updated_at' => now()]);
            DB::commit();

            // counting number of hours
            $time = Comment::where('from', auth()->user()->id)->whereDate('dated', $validated['dated'])->sum('time');

            if (convertMinutesToHours($time) > 8) {
                $message .= ' You have added more than 8 working hours';
            }
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success', 'message' => $message]);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update comment and comment id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update comment and comment id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to archive the task.',
        ]);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Comment::where('task_id', $this->taskId)->findOrFail($id)->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Comment deleted successfully.'
            ]);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while comment is deleting and comment id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while comment is deleting and comment id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
