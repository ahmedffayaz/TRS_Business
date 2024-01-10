<?php

namespace App\Livewire\Forms;
use Livewire\Form;

class SettingForm extends Form
{
    public ?string $cms_name;
    public ?string $favicon;
    public ?string $logo;

    public function rules(): array
    {
        return [
            'cms_name' => 'required',
            'favicon' => 'file|mimes:png',
            'logo' => 'file|mimes:jpeg,png,jpg',
        ];
    }
}
