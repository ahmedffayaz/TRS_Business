<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

class TermsConditionAcceptForm extends Form
{
    use WithFileUploads;

    public $id, $is_accept, $signature_file;

    public function rules(): array
    {
        return [
            'id' => 'required',
            'is_accept' => 'accepted',
            'signature_file' => 'required|mimes:jpg,png,jpeg'
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'id' => 'id',
            'is_accept' => 'accept terms & conditions',
            'signature_file' => 'signature'
        ];
    }
}
