<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{
    public function index(int $role_id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($role_id);
            $role->permission_groups = RolesController::formatPermissions($role->permissions);
            return response()->json([
                'status_code' => JsonResponse::HTTP_OK,
                'data' => $role,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Role does not exists!',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
