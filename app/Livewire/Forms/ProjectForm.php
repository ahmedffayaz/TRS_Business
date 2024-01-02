<?php

namespace App\Livewire\Forms;

use App\Models\Project;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ProjectForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $company_id;
    public ?string $client_id;
    public ?string $name;
    public ?string $start_date;
    public ?string $end_date;
    public ?string $status;
    public ?string $description;
    public ?string $budget;
    public ?string $rate_per_hour;
    public ?string $rate_unit;
    public ?string $type;
    public ?string $is_auto_archived;
    public ?string $nature;
    public ?string $reports_schedule;
    public ?string $last_updated_at;

    public function rules(): array
    {
        return [
            'company_id' => ['required'],
            'name' => ['required', 'string', 'max:191'],
            'rate_per_hour' => ['nullable', 'string', 'max:191'],
            'rate_unit' => ['nullable', 'string', 'max:191'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'company_id' => 'company',
            'client_id' => 'client',
            'name' => 'name',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'status' => 'status',
            'description' => 'description',
            'budget' => 'budget',
            'rate_per_hour' => 'rate per hour',
            'rate_unit' => 'rate unit',
            'type' => 'type',
            'is_auto_archived' => 'auto archived',
            'nature' => 'nature',
            'reports_schedule' => 'reports schedule',
            'last_updated_at' => 'last updated at',
        ];
    }

    public function set(Project $project): void
    {
        //
    }
}
