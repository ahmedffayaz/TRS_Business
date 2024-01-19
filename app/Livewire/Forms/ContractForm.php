<?php

namespace App\Livewire\Forms;

use App\Models\TermsCondition;
use Livewire\Form;

class ContractForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $title;
    public ?string $version;
    public ?string $description;
    public ?string $roles = '0';


    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'version' => ['required', 'string'],
            'description' => ['required', 'string'],
            'roles' => ['required'],
        ];
    }

    public function set(TermsCondition $contract): void
    {
        $this->title = $contract?->title;
        $this->version = $contract?->version;
        $this->description = $contract?->description;

        $this->roles = $contract->roles->pluck('id');
    }
}
