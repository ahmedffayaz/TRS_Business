<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Spatie\Permission\Models\Permission;

class PermissionForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $title;
    public ?string $group;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'group' => ['required', 'string'],
        ];
    }

    public function set(Permission $permission): void
    {
        $this->title = $permission?->title;
        $this->group = $permission?->group;
    }
}
