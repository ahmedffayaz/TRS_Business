<?php

namespace App\Livewire\Backend;


use Exception;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Component;

class UpdatePasswordComponent extends Component
{
    public $password;
    public $old_password;
    public $password_confirmation;

    protected $rules = [
        'old_password' => 'required',
        'password' => 'required|min:7|confirmed',
    ];

    #[Title('Update Password')]
    public function render()
    {
        return view('livewire.backend.update-password-component');
    }

    public function resetFormField(){
        $this->password = '';
        $this->old_password = '';
        $this->password_confirmation = '';
    }

    public function submit()
    {
        $this->validate();
        try {
            if (Hash::check($this->old_password, auth()->user()->password)) {
                auth()->user()->update([
                    'password' =>  $this->password,
                ]);
                $this->resetFormField();
                $this->dispatch('alert', ['type' => 'success',  'message' => 'Password updated successfully.']);
            } else {
                $this->dispatch('alert', ['type' => 'error',  'message' => 'Old password is invalid.']);
            }
        } catch (Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Old password is invalid.']);
        }
    }
}
