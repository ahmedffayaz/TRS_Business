<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Leave;
use Livewire\Attributes\Validate;

class LeaveForm extends Form
{
    public bool $isUpdate = false;
    public $id;
    public ?string $reason;
    public ?string $task_id;
    public $start_date;
    public  $end_date;
    public $is_working;

    public $user_id;
    public function rules(): array
    {
        $rules = [
            'reason' => 'required|string|min:10',
            'start_date' => 'required|date' ,
            'end_date' => 'required|date',
            'is_working' => 'required',
        ];
        return $rules;
    }

    public function validationAttributes(): array
    {
        return [
            'reason' => 'reason',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'is_working' => 'is_working',
        ];
    }

    public function set(Leave $leave): void
    {
        $this->id = $leave?->id;
        $this->reason = $leave?->reason;
        $this->start_date = $leave?->start_date;
        $this->end_date = $leave?->end_date;
        $this->is_working =$leave->is_working;
    }
}
