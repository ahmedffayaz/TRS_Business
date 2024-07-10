<?php

namespace App\Livewire\Forms;
use Livewire\Form;
use Livewire\WithFileUploads;

class SettingForm extends Form
{
    use WithFileUploads;
    public ?string $cms_name;
    public ?string $date_format;
    public $favicon;
    public $logoFile;
    public function rules(): array
    {
        return [
            'cms_name' => 'required',
            'date_format' => 'required',
            'favicon' => 'nullable|file|mimes:png',
            'logoFile' => 'nullable|file|mimes:jpeg,png,jpg',
        ];
    }
}
