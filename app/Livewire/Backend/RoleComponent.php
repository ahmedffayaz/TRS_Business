<?php

namespace App\Livewire\Backend;

use App\Livewire\Forms\RoleForm;
use App\Models\Business;
use App\Models\User;
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
    public int $limitPerPage = 15;
    public RoleForm $form;

    public  $permission = false;
    public $business;

    public $permissionList = [];

    public $roleName;
    private function getRoles(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : '';
        $roles= Role::where('business_id', $this->business->id)->paginate($this->limitPerPage);
        return $roles;
    }

    public function mount()
    {
        $this->business = Business::whereName(session('business'))->first();
    }
    #[Title('User Roles')]
    public function render()
    {
        $roles = $this->getRoles();
       $user = User::sessionBusiness()->where(function($query){
            $query->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client')->where('business_id', $this->business->id);
            });
        })->count();
        $isAdminRoleEditAble = auth()->user()->where(function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', 'admin')->where('business_id', $this->business->id);
            });
        })->first() ? true : false;
        // dd($isAdminRoleEditAble);
        return view('livewire.backend.role-component', compact('roles','user', 'isAdminRoleEditAble'));
    }

    public function store()
    {
        $this->form->validate();
        try {
            DB::beginTransaction();

            // Check if a role with the same name and business_id already exists
            $existingRole = Role::where('name', Str::slug($this->form->title, '-'))
            ->where('business_id', $this->business->id)
            ->first();

            if ($existingRole) throw new \Exception('A role with this name already exists for this business.');

            $role = new Role();
            $role->title = $this->form->title;
            $role->name = Str::slug($this->form->title, '-');
            $role->business_id = $this->business->id;
            $role->save();

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
        $this->permissionList = null;
        $this->permission = false;
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $role = Role::findOrFail($id);
            $this->form->set($role);
            $isShowingBusinessPermissions = $role->name == 'super_admin' ? true : false;
            $this->permissionList = $this->getGroupPermissions(null, $isShowingBusinessPermissions);
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function update($id)
    {
        $this->form->validate();
        $role = Role::findOrFail($id);
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

    public function viewPermission($id)
    {
        try {
            $role = Role::findOrFail($id);
            $isShowingBusinessPermissions = $role->name == 'super_admin' ? true : false;
            $this->permissionList = $this->getGroupPermissions($role, $isShowingBusinessPermissions);
            $this->roleName = $role->name;
            $this->permission = true;
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
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

    private function getGroupPermissions($role = null, $isShowBusinessPermissions = false)
    {
        $query = Permission::when($role, function ($query) use ($role) {
            $query->whereHas('roles', function ($query) use ($role) {
                $query->where('id', $role->id);
            });
        });

        $businessPermissions = !$isShowBusinessPermissions
            ? $query->where('group', '!=', 'business')
            : $query;

        $permissions = $businessPermissions->get();

        $permissionArray = [];

        foreach ($permissions as $permission) {
            $permissionArray[$permission->group][] = $permission;
        }

        return $permissionArray;
    }

    public function openModal()
    {
        $this->permissionList = $this->getGroupPermissions();
        $this->form->isUpdate = false;
        $this->permission = false;
        $this->openMainModal();
    }

    public function closeModal()
    {
        $this->form->isUpdate = true;
        $this->permission = false;
        $this->closeMainModal();
    }
}


