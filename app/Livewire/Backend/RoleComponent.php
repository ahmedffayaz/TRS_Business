<?php

namespace App\Livewire\Backend;

use App\Livewire\Forms\RoleForm;
use App\Traits\WithMainModal;
use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Permission;

class RoleComponent extends Component
{
    use WithPagination;
    use WithMainModal;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public RoleForm $form;

    private function getRoles(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; 
        return Role::paginate($this->limitPerPage);
    }


    #[Title('User Roles')]
    public function render()
    {
        $roles = $this->getRoles();
        $recentRoles = Role::latest()->limit(5)->get();
        return view('livewire.backend.role-component', compact('roles', 'recentRoles'));
    }

    public function store()
    {
        $this->form->validate();
        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existingCount = DB::table('roles')
                        ->where('title', $value)
                        ->count();

                    if ($existingCount > 0) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ]
        ]);
        try {
            DB::beginTransaction();
            $role = Role::create([
                'title' => $this->form->title,
                'name' => Str::slug($this->form->title, '-'),
            ]);
            if (isset($this->form->permissions)) {
                $permissions = Permission::whereIn('title', $this->form->permissions)->pluck('id');
                $role->permissions()->sync($permissions);
            }
            DB::commit();
            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Role Created Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $role = Role::findOrFail($id);
            $this->form->set($role);
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function update($id)
    {
        $this->form->validate();
        $role = Role::findOrFail($id);

        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) use ($role) {
                    $existingCount = DB::table('roles')
                        ->where('title', $value)
                        ->where('id', '!=', $role->id)
                        ->count();

                    if ($existingCount > 0) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ]
        ]);
        try {

            DB::beginTransaction();
            $role->title = $this->form->title;
            $role->name = Str::slug($this->form->title, '-');
            $role->save();

            if (isset($this->form->permissions)) {
                $permissions = Permission::whereIn('title', $this->form->permissions)->pluck('id');
                $role->permissions()->sync($permissions);
            }
            DB::commit();

            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Role Updated Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }


    public function deleteConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the client. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Role Deleted Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }
}


