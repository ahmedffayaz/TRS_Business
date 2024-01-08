<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RolesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:add_roles|edit_roles|view_roles|delete_roles|view_permissions']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('modules.settings.role.index');
    }

    /**
     * Return roles as json
     *
     * @return mixed
     * @throws Exception
     */
    public function roles()
    {
        $roles = Role::withCount(['users']);
        return DataTables::of($roles)
            ->editColumn('users_count', function ($company) {
                return wrapWithLabel($company->users_count);
            })->addColumn('actions', function ($role) {
            if ($this->auth_user->hasAnyPermission(['edit_roles', 'view_permissions', 'delete_roles'])) {
                $actions = '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
          m-dropdown-toggle="click">
            <a href="javascript:void(0)" class="m-dropdown__toggle">
              <i class="la la-ellipsis-h"></i>
            </a>
            <div class="m-dropdown__wrapper">
              <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                <div class="m-dropdown__inner">
                  <div class="m-dropdown__body">
                    <div class="m-dropdown__content">
                      <ul class="m-nav">';
                $roleid = $this->auth_user->roles;
                if ($this->auth_user->hasPermissionTo('edit_roles') && !in_array($role->id, $roleid->pluck('id')->toArray()) && $role->name != 'admin') {
                    $actions .= '<li class="m-nav__item">
                              <a href="' . route('roles.edit', $role->id) . '" class="m-nav__link">
                                <span class="m-nav__link-text">Edit Role</span>
                              </a>
                            </li>';
                }
                if ($this->auth_user->hasPermissionTo('view_permissions')) {
                    $actions .= '<li class="m-nav__item">
                              <a href="javascript:void(0)" data-id="' . $role->id . '" class="m-nav__link btn-view-permissions">
                                <span class="m-nav__link-text">View permissions</span>
                              </a>
                            </li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_roles') && $role->is_deleteable != 0 && !in_array($role->id, $roleid->pluck('id')->toArray())) {
                    $actions .= '<li class="m-nav__item">
                                <a href="' . route('roles.destroy', $role->id) . '" class="m-nav__link btn-delete" data-table="dt-bs4-roles">
                                <span class="m-nav__link-text">Delete role</span>
                                </a>
                            </li>';
                }
                $actions .= '</ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            } else {
                $actions = '<button class="btn btn-outline-dark btn-sm"  id="actions" disabled>
                    <i class="la la-lock"></i></button>';
            }
            return $actions;
        })->rawColumns(['users_count', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        $permissionGroups = RolesController::formatPermissions($permissions);
        return view('roles.create', compact('permissionGroups'));
    }

    /**
     * @param $permissions
     *
     * @return array
     */
    public static function formatPermissions($permissions)
    {
        $permissionGroups = [];
        foreach ($permissions as $permission) {
            $permissionGroups[$permission->group][] = $permission;
        }
        return $permissionGroups;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest|Request $request
     * @param Role                $role
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request, Role $role)
    {
        $input = $request->all();
        try {
            DB::beginTransaction();
            $role = $role->create([
                'name' => $input['name'],
                'title' => $input['title'],
            ]);
            $role->permissions()->sync($input['permissions']);
            DB::commit();

            flash()->success('Role created successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            flash()->error($exception->getMessage());
        }
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        try {
            $permissions = Permission::all();
            $permissionGroups = $this->formatPermissions($permissions);
            $role = Role::with('permissions')->findOrFail($id);

            if (strtolower($role->name) == 'admin') {
                flash()->error("Admin role is not editable");
                return redirect()->route('roles.index');
            }

            if (in_array($role->id, $this->auth_user->roles->pluck('id')->toArray())) {
                flash()->error("This role is not editable");
                return redirect()->route('roles.index');
            }

            $permissions = Permission::get();

            return view('roles.edit', compact('role', 'permissions', 'permissionGroups'));

        } catch (ModelNotFoundException $ex) {

            flash()->error("No role found");
            return redirect()->route('roles.index');

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleRequest|Request $request
     * @param int                 $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        try {
            $input = $request->all();

            DB::beginTransaction();
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $input['name'],
                'title' => $input['title'],
            ]);
            $role->permissions()->sync($input['permissions']);
            DB::commit();

            flash()->success('Role updated successfully');
        } catch (Exception $exception) {
            DB::rollBack();
            flash()->error($exception->getMessage());
        }
        return redirect()->route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $role = Role::findOrFail($id);
            $user = User::role($role->name)->first();
            if ($user) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => 'This role can not be deleted untill some users exist with this role.',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            if (!$role->is_deleteable) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'message' => Str::contains(strtolower($role->name), 'admin') ? 'Admin role is not deletable' : 'Default System role is not deletable',
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            DB::beginTransaction();
            $role->delete();
            DB::commit();
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Role does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Role deleted successfully',
        ], JsonResponse::HTTP_OK);
    }
}
