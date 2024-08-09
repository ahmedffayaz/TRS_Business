<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
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
                abort(403, 'Link has expired.');
            }

            $project = Project::where('slug', $decrypted_key['slug'])->firstOrFail();

            if($project->invite_link == null){
                abort(403, 'Invite link deleted.');
            }
            $logo = Storage::disk('public')->exists($project->business->logo) ? 'storage/'.$project->business->logo : cmsLogo();

            if($project){
                $encryption = Crypt::encrypt([
                    'client_id' => $project->client_id,
                    'business_id' => $project->business_id,
                    'slug' => $project->slug,
                    'logo' => $logo,
                    'business_name' => $project->client->name,
                ]);

                if (!Auth::check()) {
                    return Redirect::route('register',$encryption);
                }else{

                    $user = Auth::user();

                    if (!$user->hasRole('client')) {
                        return abort(403, 'Not client');
                    }

                    if($user->business_id === $project->business->id && $user->client_id === $project->client->id){
                        return Redirect::route('dashboard.projects.index')->with('swl_key', $encryption);
                    }else{
                        return abort(403,'Not authorized');
                    }
                }

            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid');
        }
    }
}
