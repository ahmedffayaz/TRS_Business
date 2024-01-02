<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Rule;
use App\Enums\Company\CompanyType;
use App\Models\Company;

class CompanyForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $name;
    public ?string $logo;
    public ?string $street_address;
    public ?string $city;
    public ?string $country_id;
    public ?string $postal_code;
    public ?string $parent_id;
    public ?string $type = CompanyType::PARENT->value;
    public ?string $invoice_prefix;
    public ?string $invoice_serial;

    public function rules(): array
    {
        return [
            'logo' => ['nullable'],
            'name' => ['required', 'string', 'max:191'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:191'],
            'country_id' => ['required', 'string', 'max:191'],
            'postal_code' => ['nullable', 'string', 'max:191'],
            'parent_id' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'max:191'],
            'invoice_prefix' => ['nullable', 'string', 'max:191'],
            'invoice_serial' => ['nullable', 'string', 'max:191'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'logo' => 'logo',
            'name' => 'name',
            'street_address' => 'street address',
            'city' => 'city',
            'country_id' => 'country',
            'postal_code' => 'postal code',
            'type' => 'type',
            'invoice_prefix' => 'invoice prefix',
            'invoice_serial' => 'invoice serial',
        ];
    }

    public function set(Company $company): void
    {
        $this->name = $company?->name;
        $this->logo = $company?->logo;
        $this->street_address = $company?->street_address;
        $this->city = $company?->city;
        $this->country_id = $company?->country_id;
        $this->postal_code = $company?->postal_code;
        $this->parent_id = $company?->parent_id;
        $this->invoice_prefix = $company?->invoice_prefix;
        $this->invoice_serial = $company?->invoice_serial;
    }
}
