<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use Illuminate\Contracts\Auth\Authenticatable;
use Livewire\WithFileUploads;

class UserProfileForm extends Form
{
    use WithFileUploads;

    public $id, $first_name, $last_name, $email, $phone, $address, $alternative_number, $avatar;

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email,' . $this->id . ',id',
            'phone' => 'required',
            'address' => 'required',
            'alternative_number' => 'nullable',
            'avatar' => 'nullable|mimes:jpg,png,jpeg'
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'avatar' => 'avatar',
            'first_name' => 'first name',
            'last_name' => 'last name',
        ];
    }

    public function set(Authenticatable $user): void
    {
        $this->first_name = $user?->first_name;
        $this->last_name = $user?->last_name;
        $this->email = $user?->email;
        $this->address = $user?->address;
        $this->phone = $user?->phone;
        $this->alternative_number = $user?->alternative_number;
    }
}
