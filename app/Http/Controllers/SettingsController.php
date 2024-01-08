<?php

namespace App\Http\Controllers;

use Exception;
use App\Setting;
use App\Http\Requests\SettingsRequest;

class SettingsController extends Controller
{
    public function index()
    {
        return view('systems.index');
    }

    public function create()
    {
        if(!$this->auth_user->hasPermissionTo('edit_systems')){
            abort(401);
        }
        $date_format = Setting::where('name','date_format')->first();
        $cms_name = Setting::where('name','cms_name')->first();
        return view('settings.create', compact('date_format', 'cms_name'));
    }

    public function showLoginForm()
    {
        $setting = Setting::first();
        return view('auth.login',compact('setting'));
    }

    public function store(SettingsRequest $request)
    {
        try {

            foreach($request->all() as $name => $value){
                if ($name != 'favicon') {
                    $setting[$name] = Setting::updateOrCreate([
                        'name' => $name
                    ],[
                        'name' => $name,
                        'value' => $value
                    ]);
                }
            }
            if ($request->has('favicon')) {
                $old_favicon = Setting::where('name', 'favicon')->first() ? Setting::where('name', 'favicon')->first() : Setting::create(['name' => 'favicon', 'value' => 'favicon.png']);

                if ($old_favicon->value) {
                    deleteFile($old_favicon->value);
                }

                $path = saveResizeImage($request->favicon, '/images', 32, 'png', 32 );
                $setting['favicon'] = Setting::updateOrCreate([
                    'name' => 'favicon'
                ],[
                    'value' => $path
                ]);

            }

            if ($request->has('logo')) {
                $logo = saveResizeImage($request->logo, '/images', 64, 'png', 64);
                $setting['logo']->update(['name' => 'logo', 'value' => $logo]);
            }
            return back()->with('success','Settings created successfully!');

        }catch (Exception $exception) {
            return back()->with('error',$exception->getMessage());
        }
    }
}