<?php

namespace App\Livewire\Forms;

use App\Models\TermsCondition;
use Livewire\Form;

class TermsConditionForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public $business_id;
    public ?string $title;
    public ?string $version;
    public $description;
    public ?array $roles = [];
    public ?int $created_by;
    public ?int $updated_by;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'business_id' => 'required',
                'title' => 'required|string',
                'version' => 'required|string|unique:terms_conditions,version,' . $this->id . ',id,business_id,' . $this->business_id,
                'roles' => 'required|array',
                'description' => 'required|string',
                'updated_by' => 'required'
            ];
        } else {
            return [
                'business_id' => 'required',
                'title' => 'required|string',
                'version' => 'required|string',
                'roles' => 'required|array',
                'description' => 'required|string',
                'created_by' => 'required',
                'updated_by' => 'required'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'tile' => 'title',
            'version' => 'version',
            'description' => 'description',
            'roles' => 'roles'
        ];
    }

    public function set(TermsCondition $termsCondition): void
    {
        $this->id = $termsCondition?->id;
        $this->title = $termsCondition?->title;
        $this->version = $termsCondition?->version;
        $this->description = $termsCondition?->description;

        $this->roles = $termsCondition->roles->pluck('id')->toArray();
    }
}
