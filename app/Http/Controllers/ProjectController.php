<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('modules.project.index');
    }

    public function invite($role_id, $project_slug)
    {
        if (!Auth::check()) {
            return Redirect::route('login');
        }

        try {
            $roleId = Crypt::decrypt($role_id);

            $role = Role::findOrFail($roleId);
            $project = Project::where('slug', $project_slug)->firstOrFail();
            $user = Auth::user();

            if (!$user->roles->contains($role->id)) {
                $user->roles()->attach($role->id);
            }

            return Redirect::route('dashboard.projects.detail', ['slug' => $project->slug]);

        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        }
    }
}
