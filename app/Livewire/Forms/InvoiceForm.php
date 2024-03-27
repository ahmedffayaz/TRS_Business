<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    public $id;
    public bool $isUpdate = false;

    public ?int $project_id;
    public $due_at;
    public $notes;
    public ?bool $isEmail = false;
    public ?bool $all_comments = false;
    public $deduction;

    public ?array $description;
    public ?array $quantity;
    public ?array $rate;
    public ?array $amount;

    public function rules(): array
    {
        return [
            'project_id' => 'required|int',
            'due_at' => 'required|date',
            'deduction' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'isEmail' => 'nullable|bool',
            'all_comments' => 'nullable|bool'
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'project_id' => 'project',
            'due_at' => 'due date',
            'isEmail' => 'send email',
            'all_comments' => 'Select/De-select all comments '
        ];
    }
}
