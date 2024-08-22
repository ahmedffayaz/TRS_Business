<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    public $id;
    public bool $isUpdate = false;

    public ?int $project_id;
    public ?bool $isEmail = false;
    public ?bool $all_comments = false;
    public $total_amount;
    public ?array $task;
    public ?array $generic_comments;
    public ?string $due_at;
    public ?string $date;
    public $deduction;
    public  $description;
    public ?array $quantity;
    public ?array $rate;
    public ?array $amount;
    public $currency ;

    public function rules(): array
    {
        $rules = [
            'project_id' => 'required|int',
            'isEmail' => 'nullable|bool',
            'all_comments' => 'nullable|bool',
            'task' => 'nullable|array',
            'generic_comments' => 'nullable|array',
            'due_at' => 'required|date',
            'deduction' => 'nullable|numeric',
            'description' => 'nullable|string',
            'currency' => 'required|string'
        ];

        // Add custom validation rule for total_amount
        if (!is_null($this->total_amount)) {
            $rules['total_amount'] = 'required|numeric|min:0.01';
        } else {
            $rules['total_amount'] = 'nullable';
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        return [
            'project_id' => 'project',
            'isEmail' => 'send email',
            'all_comments' => 'Select/De-select all comments',
            'total_amount' => 'task comment',
            'due_at' => 'due date',
            'deduction' => 'adjustment amount',
            'description' => 'notes',
            'currency' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'total_amount.required' => 'Must select atleast one billable task comment',
            'total_amount.numeric' => 'Must select atleast one billable task comment',
            'total_amount.min' => 'Must select atleast one billable task comment',
        ];
    }
}
