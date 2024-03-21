<?php

namespace App\Livewire\Backend\Task;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Livewire\Forms\TaskForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskDataComponent extends Component
{
    use WithPagination, WithMainModal;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 20;

    public ?int $projectId;

    public TaskForm $form;

    public function mount($project = null)
    {
        $this->projectId = $project ? $project : null;
    }

    private function getTasksQuery()
    {
        $projectId = isset($this->projectId) ? $this->projectId : null;
        return Task::hasProject($projectId)->with(['project'])
            ->getList($this->search, $this->columnName, $this->sortDirection);
    }

    private function getTotalTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->onlyTrashed()->paginate($this->limitPerPage);
    }

    private function getTasks(): LengthAwarePaginator
    {
        if ($this->dataCountType === 'total')
            return $this->getTotalTasks();
        else if ($this->dataCountType === 'active')
            return $this->getActiveTasks();
        else if ($this->dataCountType === 'archived')
            return $this->getArchivedTasks();
    }

    public function render()
    {
        $projectId = isset($this->projectId) ? $this->projectId : null;
        $tasks = $this->getTasks();
        $totalTasks = Task::hasProject($projectId)->withTrashed()->count();
        $totalActiveTasks = Task::hasProject($projectId)->count();
        $totalArchivedTasks = Task::hasProject($projectId)->onlyTrashed()->count();

        $projects = null;
        if(auth()->user()->hasRole('super-admin')){
            $projects = Project::sessionBusiness()->pluck('name', 'id')->all();
        } else {
            if (auth()->user()->hasPermissionTo('view_projects')) {
                $projects = Project::sessionBusiness()->pluck('name', 'id')->all();
            } else if (auth()->user()->hasPermissionTo('view_associated_projects')) {
                $projects = Project::sessionBusiness()->whereHas('members', function($query)
                {
                  $query->where('user_id','=', auth()->user()->id);
                 })->pluck('name', 'id')->all();
            }
        }

        $members = User::sessionBusiness()->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'client');
                })->get()->pluck('nameWithDesignation', 'id');

        $this->dispatch('reinitialize-icons');

        return view('livewire.backend.task.task-data-component', compact('tasks', 'totalTasks', 'totalActiveTasks', 'totalArchivedTasks', 'projects', 'members'));
    }

    public function openModal()
    {
        $this->openMainModal();
    }

    public function closeModal()
    {
        $this->closeMainModal();
        $this->dispatch('project-select', ['formProject' => []]);
        $this->dispatch('assigned-member-select', ['formUser' => []]);
    }

    public function store()
    {
        if (isset($this->projectId))
            $this->form->project_id = $this->projectId;

        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            Task::create([
                'project_id' => $validated['project_id'],
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date']
            ]);
            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Task created successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while create task: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create task: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        try {
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);

            $this->form->set($verifiedTask);

            $this->dispatch('project-select', ['formProject' => $verifiedTask->project_id]);
            $this->dispatch('assigned-member-select', ['formUser' => $verifiedTask->user_id]);

            $this->openMainModal();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while edit task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);

            DB::beginTransaction();
            $verifiedTask->update([
                'project_id' => $validated['project_id'],
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date']
            ]);
            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Task updated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function archiveConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'archive',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to archive the task.',
        ]);
    }

    #[On('archive')]
    public function archive($id)
    {
        try {
            DB::beginTransaction();
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);
            $verifiedTask->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Task archived successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while task archive and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while task archive and task id is ' . $id . ' ' . $exception->getMessage());
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
            'description' => 'You are about to delete the task. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);
            $verifiedTask->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Task deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while task delete and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while task delete and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
