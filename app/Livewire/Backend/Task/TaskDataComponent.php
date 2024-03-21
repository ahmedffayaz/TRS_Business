<?php

namespace App\Livewire\Backend\Task;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

class TaskDataComponent extends Component
{
    use WithPagination;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 20;

    public ?int $projectId;

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

    private function getTotalTasks() : LengthAwarePaginator
    {
        return $this->getTasksQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveTasks() : LengthAwarePaginator
    {
        return $this->getTasksQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedTasks() : LengthAwarePaginator
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

        return view('livewire.backend.task.task-data-component', compact('tasks', 'totalTasks', 'totalActiveTasks', 'totalArchivedTasks'));
    }
}
