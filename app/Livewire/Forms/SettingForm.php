<?php

namespace App\Livewire\Forms;
use Livewire\Form;

class SettingForm extends Form
{
    public ?string $cms_name;
    public ?string $date_format;
    public ?string $faviconFile;
    public ?string $logoFile;

    public function rules(): array
    {
        return [
            'cms_name' => 'required',
            'date_format' => 'required',
            'faviconFile' => 'nullable|file|mimes:png',
            'logoFile' => 'nullable|file|mimes:jpeg,png,jpg',
        ];
    }
}
