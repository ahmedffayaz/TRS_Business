<?php

namespace App\Livewire\Backend;

use App\Models\Role;
use App\Models\User;
use Exception;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class RoleComponent extends Component
{
    public function render()
    {
        return view('livewire.backend.role-component');
    }

    // public function __construct()
    // {
    //     parent::__construct();
    //     $this->middleware(['permission:add_roles|edit_roles|view_roles|delete_roles|view_permissions']);
    // }

    public function roles()
    {
        $roles = Role::withCount(['users'])->get();
    }

    public function create()
    {
        $permissions = Permission::all();
        // $permissionGroups = RolesController::formatPermissions($permissions);
        return view('roles.create', compact('permissionGroups'));
    }

    public static function formatPermissions($permissions)
    {
        $permissionGroups = [];
        foreach ($permissions as $permission) {
            $permissionGroups[$permission->group][] = $permission;
        }
        return $permissionGroups;
    }

    public function store(Role $role)
    {
        $input = $this->form->all();
        try {
            DB::beginTransaction();
            $role = $role->create([
                'name' => $input['name'],
                'title' => $input['title'],
            ]);
            $role->permissions()->sync($input['permissions']);
            DB::commit();

            // flash()->success('Role created successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            // flash()->error($exception->getMessage());
        }
        return redirect()->route('roles.index');
    }

    public function edit(int $id)
    {
        try {
            $permissions = Permission::all();
            $permissionGroups = $this->formatPermissions($permissions);
            $role = Role::with('permissions')->findOrFail($id);

            if (strtolower($role->name) == 'admin') {
                // flash()->error("Admin role is not editable");
                return redirect()->route('roles.index');
            }

            if (in_array($role->id, $this->auth_user->roles->pluck('id')->toArray())) {
                // flash()->error("This role is not editable");
                return redirect()->route('roles.index');
            }

            $permissions = Permission::get();

            return view('roles.edit', compact('role', 'permissions', 'permissionGroups'));

        } catch (Exception $ex) {

            // flash()->error("No role found");
            return redirect()->route('roles.index');

        }
    }

    public function update($id)
    {
        try {
            $input = $this->form->all();

            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $input['name'],
                'title' => $input['title'],
            ]);
            $role->permissions()->sync($input['permissions']);
            DB::commit();

            // flash()->success('Role updated successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            // flash()->error($exception->getMessage());
        }
        return redirect()->route('roles.index');
    }

    public function destroy(int $id)
    {
        try {
            $role = Role::findOrFail($id);
            $user = User::role($role->name)->first();
            if ($user) {
                return response()->json([
                    'message' => 'This role can not be deleted until some users exist with this role.',
                ]);
            }
            if (!$role->is_deleteable) {
                return response()->json([
                    'message' => Str::contains(strtolower($role->name), 'admin') ? 'Admin role is not deletable' : 'Default System role is not deletable',
                ]);
            }
            DB::beginTransaction();
            $role->delete();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }
}

