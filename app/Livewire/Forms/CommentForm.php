<?php

namespace App\Livewire\Forms;

use App\Enums\Comment\CommentUnit;
use Livewire\Form;
use App\Models\Comment;
use Livewire\Attributes\Validate;

class CommentForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $description;
    public ?string $task_id;
    public $time;
    public ?string $unit = null;
    public ?bool $is_billable;
    public $dated;

    public function rules(): array
    {
        $rules = [
            'description' => 'required|string|min:10',
            'unit' => 'required|string',
            'is_billable' => 'nullable|boolean',
            'dated' => 'required|date'
        ];

        if ($this->unit === CommentUnit::MINUTES->value) {
            // If unit is mins, add validation for min length 10
            $rules['time'] = 'required|integer|min:10';
        } else {
            // If unit is not mins, add validation for min length 1
            $rules['time'] = 'required|numeric|min:0.17';
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        return [
            'description' => 'description',
            'time' => 'time',
            'unit' => 'unit',
            'dated' => 'date',
            'is_billable' => 'is billable'
        ];
    }

    public function set(Comment $comment): void
    {
        $this->id = $comment?->id;
        $this->task_id = $comment?->task_id;
        $this->description = $comment?->description;
        $this->time = $comment?->time;
        $this->is_billable = $comment?->is_billable;
        $this->dated = $comment?->dated;
    }
}
