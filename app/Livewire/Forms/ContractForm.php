<?php

namespace App\Livewire\Forms;

use App\Models\Contract;
use Livewire\Form;

class ContractForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $title;
    public ?string $version;
    public ?string $description;
    public ?array $roles;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'version' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    public function set(Contract $contract): void
    {
        $this->title = $contract?->title;
        $this->version = $contract?->version;
        $this->description = $contract?->description;
    }
}
