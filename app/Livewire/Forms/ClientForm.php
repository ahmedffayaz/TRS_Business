<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Livewire\Attributes\Rule;
use Livewire\Form;

class ClientForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $business_id;
    public ?string $name;
    public ?string $slug;
    public ?string $address;
    public ?string $city;
    public ?string $country_id;
    public ?string $postal_code;
    public ?string $rate_per_hour;
    public ?string $rate_unit;
    public ?string $note;
    public ?string $add_user;

    public ?array $first_name;
    public ?array $last_name;
    public ?array $email;
    public ?array $phone;
    public ?array $password;
    public ?array $send_email;

    public function rules(): array
    {
        if ($this->isUpdate) {
            return [
                'business_id' => 'required|string|max:191',
                'name' => 'required|string|max:191|unique:clients,name,' . $this->id . ',id',
                'address' => 'nullable|string|max:500',
                'city' => 'required|string|max:191',
                'country_id' => 'required|string|max:191',
                'postal_code' => 'nullable|string|max:191',
                'rate_per_hour' => 'nullable|string|max:191',
                'rate_unit' => 'nullable|string|max:191',
                'note' => 'nullable|string',
                'add_user' => 'nullable|string'
            ];
        } else {
            return [
                'business_id' => 'required|string|max:191',
                'name' => 'required|string|max:191|unique:clients',
                'address' => 'nullable|string|max:500',
                'city' => 'required|string|max:191',
                'country_id' => 'required|string|max:191',
                'postal_code' => 'nullable|string|max:191',
                'rate_per_hour' => 'nullable|string|max:191',
                'rate_unit' => 'nullable|string|max:191',
                'note' => 'nullable|string',
                'add_user' => 'nullable|string'
            ];
        }
    }

    public function validationAttributes(): array
    {
        return [
            'business_id' => 'business',
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
        $this->business_id = $client?->business_id;
        $this->name = $client?->name;
        $this->address = $client?->address;
        $this->city = $client?->city;
        $this->country_id = $client?->country_id;
        $this->postal_code = $client?->postal_code;
        $this->rate_per_hour = $client?->rate_per_hour;
        $this->rate_unit = $client?->rate_unit;
        $this->note = $client?->note;
    }
}
