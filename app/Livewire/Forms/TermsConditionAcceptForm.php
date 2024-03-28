<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithFileUploads;

class TermsConditionAcceptForm extends Form
{
    use WithFileUploads;

    public $id, $is_accept, $signature_file, $digital_signature_pad;

    public function rules(): array
    {
        return [
            'id' => 'required',
            'is_accept' => 'accepted',
            'digital_signature_pad' => 'nullable|string',
            'signature_file' => 'nullable|required_if:digital_signature_pad,null|mimes:jpg,png,jpeg'
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'id' => 'id',
            'is_accept' => 'accept terms & conditions',
            'signature_file' => 'signature',
            'digital_signature_pad' => 'signature'
        ];
    }

    public function messages(): array
    {
        return [
            'signature_file.required_if' => 'Signature field is required.',
        ];
    }
}
