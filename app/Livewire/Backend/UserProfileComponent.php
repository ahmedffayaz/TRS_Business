<?php

namespace App\Livewire\Backend;

use Exception;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class UserProfileComponent extends Component
{
    use WithFileUploads;

    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $alternative_number;

    protected $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
        'alternative_number' => 'nullable',
    ];


    public function mount(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->address = $user->address;
        $this->alternative_number = $user->alternative_number;
    }


    #[Title('User Profile')]
    public function render()
    {
        return view('livewire.backend.user-profile-component');
    }

    public function submit(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $validatedData = $this->validate();
        try {
            $this->rules['email'] = 'required|email|' . Rule::unique('users', 'email')->ignore($user->id);
            $user->update($validatedData);
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Profile updated successfully.']);
        } catch (Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    
}
