<?php
namespace App\Livewire\Backend;

use App\Libraries\ImageManager;
use App\Livewire\Forms\SettingForm;
use App\Models\Setting;
use Livewire\Attributes\Title;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Title('System Settings')]
class SettingComponent extends Component
{
    use WithFileUploads;
    public $logo;
    public $favicon;
    public $cms_name;
    public $date_format;
    public SettingForm $form;
    public string $defaultFavicon = 'assets/images/avatar.png';
    public string $defaultLogo = 'assets/images/avatar.png';
    public $cmsFavicon;
    public $cmsLogo;
    public $imagePath = 'cms/images';

    public function mount()
    {
        $favicon = Setting::where('name', 'cms_favicon')->pluck('value')->first();
        if ($favicon && Storage::disk('public')->exists('cms/images/' . $favicon))
            $this->cmsFavicon = $favicon;

        $logo = Setting::where('name', 'cms_logo')->pluck('value')->first();
        if ($logo && Storage::disk('public')->exists('cms/images/' . $logo))
            $this->cmsLogo = $logo;

        $this->form->date_format = Setting::where('name', 'date_format')->pluck('value')->first();
        $this->form->cms_name = Setting::where('name', 'cms_name')->pluck('value')->first();
    }

    public function render()
    {
        if (!auth()->user()->hasPermissionTo('edit_systems')) {
            abort(401);
        }
        return view('livewire.backend.setting-component');
    }


    public function submit()
    {
        $validated = $this->form->validate();
        try {
            $request = $this->form->all();
            $setting = [];

            foreach ($request as $name => $value) {
                if ($name != 'favicon' && $name != 'logoFile') {
                    $setting[$name] = Setting::updateOrCreate(
                        ['name' => $name],
                        ['name' => $name, 'value' => $value]
                    );
                }
            }

            $imageManager = new ImageManager();

            if (array_key_exists('favicon', $request) && $request['favicon']) {
                $validated['favicon'] = $imageManager->setFile($validated['favicon'])->resize(32)->setDirectory($this->imagePath)->save();
                $setting['favicon'] = Setting::updateOrCreate(
                    ['name' => 'cms_favicon'],
                    ['value' => $validated['favicon']]
                );
            }

            if (array_key_exists('logoFile', $request) && $request['logoFile']) {
                $validated['logoFile'] = $imageManager->setFile($validated['logoFile'])->resize(150)->setDirectory($this->imagePath)->save();
                $setting['logo'] = Setting::updateOrCreate(
                    ['name' => 'cms_logo'],
                    ['value' => $validated['logoFile']]
                );
            }

            $this->dispatch('alert', ['type' => 'success',  'message' => 'Settings created successfully!']);
        } catch (Exception $exception) {
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong. Please try again later.' . $exception->getMessage()]);
        }
    }
}
