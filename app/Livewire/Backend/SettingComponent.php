<?php
namespace App\Livewire\Backend;

use App\Livewire\Forms\SettingForm;
use App\Models\Setting;
use Livewire\Attributes\Title;
use Exception;
use Livewire\Component;

class SettingComponent extends Component
{
    public $date_format;
    public $cms_name;
    public SettingForm $form;


    public function mount()
    {
        $this->date_format = Setting::where('name', 'date_format')->first();
        $this->cms_name = Setting::where('name', 'cms_name')->first();
    }

    #[Title('Settings')]
    public function render()
    {
        if (!auth()->user()->hasPermissionTo('edit_systems')) {
            abort(401);
        }
        return view('livewire.backend.setting-component');
    }


    public function store()
    {
        try {
            $this->form->validate();

            $request = $this->form->all();
            $setting = [];

            foreach ($request as $name => $value) {
                if ($name != 'favicon') {
                    $setting[$name] = Setting::updateOrCreate(
                        ['name' => $name],
                        ['name' => $name, 'value' => $value]
                    );
                }
            }

            if (array_key_exists('favicon', $request) && $request['favicon']) {
                $old_favicon = Setting::where('name', 'favicon')->firstOr(function () {
                    return Setting::create(['name' => 'favicon', 'value' => 'favicon.png']);
                });

                if ($old_favicon->value) {
                    deleteFile($old_favicon->value);
                }

                $path = saveResizeImage($request['favicon'], '/images', 32, 'png', 32);
                $setting['favicon'] = Setting::updateOrCreate(
                    ['name' => 'favicon'],
                    ['value' => $path]
                );
            }

            if (array_key_exists('logo', $request) && $request['logo']) {
                $logo = saveResizeImage($request['logo'], '/images', 64, 'png', 64);
                $setting['logo'] = Setting::updateOrCreate(
                    ['name' => 'logo'],
                    ['value' => $logo]
                );
            }

            session()->flash('success', 'Settings created successfully!');
        } catch (Exception $exception) {
            session()->flash('error', 'Something went wrong. Please try again later.');
        }
    }
}
