<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Livewire\Attributes\Rule;
use Illuminate\Validation\Rules\Password;

class UserForm extends Form
{
    public bool $isUpdate = false;
    public $id;

    public ?string $avatar;
    public string $first_name = '';
    public ?string $middle_name;
    public string $last_name = '';
    public ?string $email;
    public ?string $alternative_email;
    public ?string $designation;
    public ?string $salary;
    public ?string $currency;
    public ?string $password;
    public ?string $password_confirmation;
    public ?string $address;
    public ?string $phone;
    public ?string $alternative_number;
    public ?string $company_id;
    public ?int $is_active;

    public function rules(): array
    {
        return [
            'avatar' => ['nullable'],
            'first_name' => ['required', 'string', 'max:191'],
            'middle_name' => ['nullable', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email:strict', 'unique:users,email,' . $this->id, 'max:191'],
            'alternative_email' => ['nullable', 'string', 'email:strict,dns', 'unique:users,alternative_email,' . $this->id, 'max:191'],
            'designation' => ['required', 'string', 'max:191'],
            'salary' => ['nullable', 'numeric', 'between:0,99999999.99'],
            'currency' => ['nullable', 'string'],
            'password' => [$this->isUpdate ? 'nullable' : 'required', 'string', Password::min(6), 'max:191', 'confirmed'],
            'password_confirmation' => [$this->isUpdate ? 'nullable' : 'required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:191'],
            'alternative_number' => ['nullable', 'string', 'unique:users,alternative_number,' . $this->id, 'max:191'],
            'company_id' => ['required'],
            'is_active' => ['required']
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'avatar' => 'avatar',
            'first_name' => 'first name',
            'middle_name' => 'middle name',
            'last_name' => 'last name',
            'email' => 'email',
            'alternative_email' => 'alternative email',
            'designation' => 'designation',
            'salary' => 'salary',
            'currency' => 'currency',
            'password' => 'password',
            'password_confirmation' => 'confirm password',
            'address' => 'address',
            'phone' => 'phone',
            'alternative_number' => 'alternative_number',
            'company_id' => 'company',
            'is_active' => 'status'
        ];
    }

    public function set(User $user): void
    {
        $this->first_name = $user?->first_name;
        $this->middle_name = $user?->middle_name;
        $this->last_name = $user?->last_name;
        $this->email = $user?->email;
        $this->alternative_email = $user?->alternative_email;
        $this->designation = $user?->designation;
        $this->salary = $user?->salary;
        $this->currency = $user?->currency;
        $this->address = $user?->address;
        $this->phone = $user?->phone;
        $this->alternative_number = $user?->alternative_number;
        $this->company_id = $user?->company_id;
        $this->is_active = $user?->is_active;
    }
}
