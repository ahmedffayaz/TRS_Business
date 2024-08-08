<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

    public function invite($encrypted)
    {
        try {
            $decrypted_key = Crypt::decrypt($encrypted);
            $expiresAt = Carbon::createFromTimestamp($decrypted_key['expires_at']);

            if (Carbon::now()->greaterThanOrEqualTo($expiresAt)) {
                abort(404, 'Link has expired.');
            }

            $project = Project::where('slug', $decrypted_key['slug'])->firstOrFail();

            if($project){
                $encryption = Crypt::encrypt([
                    'client_id' => $project->client_id,
                    'business_id' => $project->business_id,
                    'slug' => $project->slug,
                ]);

                if (!Auth::check()) {
                    return Redirect::route('register',$encryption);
                }
            }else{
                abort(404);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(404, 'Invalid');
        }
    }
}
