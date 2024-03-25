<?php

namespace App\Livewire\Forms;

use App\Enums\Task\TaskPriority;
use App\Models\Task;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaskForm extends Form
{
    public ?bool $isUpdate = false;
    public ?int $id;

    public ?string $name;
    public $description;
    public ?string $user_id;
    public ?string $project_id;
    public ?string $priority;
    public ?string $start_date;
    public ?string $end_date;
    public ?string $completed_at;
    public ?array $attachments;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'name' => 'required|string|max:191',
                'description' => 'nullable|string',
                'user_id' => 'required',
                'project_id' => 'required',
                'priority' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'completed_at' => 'nullable|datetime',
                'attachments' => 'nullable'
            ];
        } else {
            return [
                'name' => 'required|string|max:191',
                'description' => 'nullable|string',
                'user_id' => 'required',
                'project_id' => 'required',
                'priority' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'completed_at' => 'nullable|datetime',
                'attachments' => 'nullable'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'name' => 'name',
            'description' => 'description',
            'user_id' => 'assigned to',
            'project_id' => 'project',
            'priority' => 'priority',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'completed_at' => 'completed at'
        ];
    }

    public function set(Task $task): void
    {
        $this->id = $task->id;
        $this->name = $task->name;
        $this->description = $task->description;
        $this->user_id = $task->user_id;
        $this->project_id = $task->project_id;
        $this->priority = $task->priority;
        $this->start_date = $task->start_date;
        $this->end_date = $task->end_date;
        $this->completed_at = $task->completed_at;
    }
}
