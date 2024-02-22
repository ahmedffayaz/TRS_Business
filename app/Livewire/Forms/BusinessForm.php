<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Business;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class BusinessForm extends Form
{
    use WithFileUploads;

    public bool $isUpdate = false;
    public $id;

    public ?string $name;
    public $roles = [];
    public $logo;
    public ?string $address;
    public ?string $city;
    public ?string $country_id;
    public ?string $postal_code;
    public ?string $invoice_prefix;
    public ?string $invoice_serial;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'name' => 'required|min:3|unique:businesses,name,' . $this->id . ',id',
                'roles' => 'required',
                'logo' => 'nullable|mimes:jpg,png,jpeg,bmp',
                'address' => 'required|string|max:200',
                'city' => 'required|string|max:191',
                'country_id' => 'required|string',
                'postal_code' => 'required_if:type,business|max:20',
                'invoice_prefix' => 'required_if:type,business',
                'invoice_serial' => 'required_if:type,business'
            ];
        }
        else {
            return [
                'name' => 'required|min:3|unique:businesses',
                'roles' => 'required',
                'logo' => 'nullable|mimes:jpg,png,jpeg,bmp',
                'address' => 'required|string|max:200',
                'city' => 'required|string|max:191',
                'country_id' => 'required|string',
                'postal_code' => 'required_if:type,business|max:20',
                'invoice_prefix' => 'required_if:type,business',
                'invoice_serial' => 'required_if:type,business'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'name' => 'name',
            'logo' => 'logo',
            'address' => 'address',
            'city' => 'city',
            'country_id' => 'country',
            'postal_code' => 'postal address',
            'invoice_prefix' => 'invoice prefix',
            'invoice_serial' => 'invoice serial'
        ];
    }

    public function set(Business $business): void
    {
        $this->id = $business?->id;
        $this->name = $business?->name;
        $this->address = $business?->address;
        $this->city = $business?->city;
        $this->country_id = $business?->country_id;
        $this->postal_code = $business?->postal_code;
        $this->invoice_prefix = $business?->invoice_prefix;
        $this->invoice_serial = $business?->invoice_serial;
    }
}
