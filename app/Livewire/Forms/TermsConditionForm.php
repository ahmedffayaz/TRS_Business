<?php

namespace App\Livewire\Forms;

use App\Models\TermsCondition;
use Livewire\Form;

class TermsConditionForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $title;
    public ?string $version;
    public ?string $description;
    public ?array $roles = [];


    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'version' => ['required', 'string'],
            'description' => ['required', 'string'],
            'roles' => ['required','array'],
        ];
    }

    public function set(TermsCondition $termsCondition): void
    {
        $this->title = $termsCondition?->title;
        $this->version = $termsCondition?->version;
        $this->description = $termsCondition?->description;

        $this->roles = $termsCondition->roles->pluck('id')->toArray();
    }
}
