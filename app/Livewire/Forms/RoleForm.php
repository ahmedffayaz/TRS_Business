<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $title;
    public ?array $permissions;

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'permissions.*' => 'nullable',
        ];
    }

    public function set(Role $role): void
    {
        $this->title = $role?->title;
        $this->permissions = $role?->permissions->pluck('title')->toArray();
    }
}
