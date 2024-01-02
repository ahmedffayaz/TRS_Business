<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ClientForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $company_id;
    public ?string $name;
    public ?string $street_address;
    public ?string $city;
    public ?string $country_id;
    public ?string $postal_code;
    public ?string $rate_per_hour;
    public ?string $rate_unit;
    public ?string $note;

    public function rules(): array
    {
        return [
            'company_id' => ['required'],
            'name' => ['required', 'string', 'max:191'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:191'],
            'country_id' => ['required', 'string', 'max:191'],
            'postal_code' => ['nullable', 'string', 'max:191'],
            'rate_per_hour' => ['nullable', 'string', 'max:191'],
            'rate_unit' => ['nullable', 'string', 'max:191'],
            'note' => ['nullable', 'string'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'company_id' => 'company',
            'name' => 'name',
            'street_address' => 'street address',
            'city' => 'city',
            'country_id' => 'country',
            'postal_code' => 'postal code',
            'rate_per_hour' => 'rate per hour',
            'rate_unit' => 'rate unit',
            'note' => 'note',
        ];
    }

    public function set(Client $client): void
    {
        $this->company_id = $client?->company_id;
        $this->name = $client?->name;
        $this->street_address = $client?->street_address;
        $this->city = $client?->city;
        $this->country_id = $client?->country_id;
        $this->postal_code = $client?->postal_code;
        $this->rate_per_hour = $client?->rate_per_hour;
        $this->rate_unit = $client?->rate_unit;
        $this->note = $client?->note;
    }
}
