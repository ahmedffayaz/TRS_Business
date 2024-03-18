<?php

namespace App\Livewire\Forms;

use App\Models\Project;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ProjectForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?int $business_id;
    public ?string $client_id;
    public ?string $name;
    public $description;
    public ?string $start_date;
    public ?string $end_date;
    public ?string $type;
    public ?string $nature;
    public ?string $budget;
    public ?string $hourly_rate;
    public ?string $currency;
    public ?string $status;
    public ?string $is_auto_archive;
    public ?array $members;
    public ?array $reports_schedule;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'business_id' => 'required|integer|max:191',
                'client_id' => 'required',
                'name' => 'required|string|max:191|unique:projects,name,' . $this->id . ',id',
                'description' => 'nullable',
                'start_date' => 'required|date|after:yesterday',
                'end_date' => 'nullable|date|after:start_date',
                'type' => 'required',
                'nature' => 'required',
                'budget' => 'required',
                'hourly_rate' => 'required',
                'currency' => 'required',
                'status' => 'required',
                'is_auto_archive' => 'required',
                'members' => 'nullable',
                'reports_schedule' => 'nullable'
            ];
        } else {
            return [
                'business_id' => 'required|integer|max:191',
                'client_id' => 'required',
                'name' => 'required|string|max:191|unique:projects',
                'description' => 'nullable',
                'start_date' => 'required|date|after:yesterday',
                'end_date' => 'nullable|date|after:start_date',
                'type' => 'required',
                'nature' => 'required',
                'budget' => 'required',
                'hourly_rate' => 'required',
                'currency' => 'required',
                'status' => 'required',
                'is_auto_archive' => 'required',
                'members' => 'nullable',
                'reports_schedule' => 'nullable'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'client_id' => 'client',
            'name' => 'project name',
            'description' => 'description',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'type' => 'type',
            'nature' => 'project nature',
            'budget' => 'budget',
            'hourly_rate' => 'hourly rate',
            'currency' => 'currency',
            'status' => 'status',
            'is_auto_archive' => 'project auto archive status',
            'members' => 'members',
            'reports_schedule' => 'reports schedule'
        ];
    }

    public function set(Project $project): void
    {
        $this->client_id = $project->client_id;
        $this->name = $project->name;
        $this->description = $project->description;
        $this->start_date = $project->start_date;
        $this->end_date = $project->end_date;
        $this->type = $project->type->value;
        $this->nature = $project->nature->value;
        $this->budget = $project->budget;
        $this->hourly_rate = $project->hourly_rate;
        $this->currency = $project->currency;
        $this->status = $project->status->value;
        $this->is_auto_archive = $project->is_auto_archive;
        $this->reports_schedule = explode(',', $project->reports_schedule);
    }
}
