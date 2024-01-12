<?php
namespace App\Livewire\Backend;

use App\Livewire\Forms\SettingForm;
use App\Models\Setting;
use Livewire\Attributes\Title;
use Exception;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class SettingComponent extends Component
{
    public $logo;
    public $favicon;
    public $cms_name;
    public $date_format;
    public SettingForm $form;


    public function mount()
    {
        $favicon = Setting::where('name', 'favicon')->pluck('value')->first();
        $faviconImage = $favicon ? Storage::url($favicon) : '/assets/images/avatar.png';

        
        $logo = Setting::where('name', 'logo')->pluck('value')->first();
        $logoImage = $logo ? Storage::url($logo) : '/assets/images/avatar.png';

        $this->favicon = $faviconImage;
        $this->logo = $logoImage;
        $this->form->date_format = Setting::where('name', 'date_format')->pluck('value')->first();
        $this->form->cms_name = Setting::where('name', 'cms_name')->pluck('value')->first();
    }

    #[Title('Settings')]
    public function render()
    {
        if (!auth()->user()->hasPermissionTo('edit_systems')) {
            abort(401);
        }
        return view('livewire.backend.setting-component');
    }


    public function submit()
    {
        $this->form->validate();
        try {
            $request = $this->form->all();
            $setting = [];

            foreach ($request as $name => $value) {
                if ($name != 'faviconFile' && $name != 'logoFile') {
                    $setting[$name] = Setting::updateOrCreate(
                        ['name' => $name],
                        ['name' => $name, 'value' => $value]
                    );
                }
            }

            if (array_key_exists('faviconFile', $request) && $request['faviconFile']) {
                $old_favicon = Setting::where('name', 'favicon')->pluck('value')->first();
                if (isset($old_favicon)) {
                    deleteFile($old_favicon);
                }

                $path = saveResizeImage($request['faviconFile'], '/images', 32, 'png', 32);
                $setting['favicon'] = Setting::updateOrCreate(
                    ['name' => 'favicon'],
                    ['value' => $path]
                );
            }

            if (array_key_exists('logoFile', $request) && $request['logoFile']) {
                $old_logo = Setting::where('name', 'logo')->pluck('value')->first();
                if (isset($old_logo)) {
                    deleteFile($old_logo);
                }

                $logo = saveResizeImage($request['logoFile'], '/images', 64, 'png', 64);
                $setting['logo'] = Setting::updateOrCreate(
                    ['name' => 'logo'],
                    ['value' => $logo]
                );
            }

            $this->dispatch('alert', ['type' => 'success',  'message' => 'Settings created successfully!']);
        } catch (Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong. Please try again later.' . $exception->getMessage()]);
        }
    }
}
