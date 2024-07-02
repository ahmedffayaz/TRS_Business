<?php

namespace App\Livewire\Forms;

use App\Enums\Comment\CommentUnit;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ChatHourForm extends Form
{
    public $id;
    public ?string $task_id;
    public $time;
    public ?string $unit = null;
    public ?bool $is_billable;
    public $dated;
    public $isHourModalVisible = false;

    public $description;
    public function rules(): array
    {
        $rules = [
            'unit' => $this->isHourModalVisible ? 'required|string' : 'nullable',
            'is_billable' => 'nullable|boolean',
            'dated' => $this->isHourModalVisible ? 'required|date' : 'nullable',
            'description' => 'nullable|string'
        ];

        if ($this->unit === CommentUnit::MINUTES->value) {
            // If unit is mins, add validation for min length 10
            $rules['time'] = $this->isHourModalVisible ? 'required|integer|min:10' : 'nullable';
        } else {
            // If unit is not mins, add validation for min length 1
            $rules['time'] = $this->isHourModalVisible ? 'required|numeric|min:0.17' : 'nullable';
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        return [
            'time' => 'time',
            'unit' => 'unit',
            'dated' => 'date',
            'is_billable' => 'is billable',
            'description' => 'message'
        ];
    }
}
