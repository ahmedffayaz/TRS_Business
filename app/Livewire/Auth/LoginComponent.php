<?php

namespace App\Livewire\Auth;

use App\Enums\User\AccountType;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class LoginComponent extends Component
{
    public $email;
    public $password;
    public $remember;
    public LoginForm $form;

    #[Layout('components.layouts.guest')]
    public function render()
    {
        return view('livewire.auth.login-component');
    }

    public function login()
    {
        $validated = $this->form->validate();
        if (Auth::attempt($validated, $this->remember)) {
            if (!auth()->user()->is_active) {
                Auth::logout();
                session()->forget('business');
                session()->flash('error', 'Account not active, Please contact admin.');
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Account not active, Please contact admin.']);
                return redirect()->to('/');
            }
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Login successfully.']);
            return redirect()->route('dashboard.home');
        } else {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Invalid credentials.']);
            session()->flash('error', 'Invalid credentials');
        }
    }

    public function logout()
    {
        Auth::logout();
        session()->forget('business');
        session()->flash('status', 'Logout successful');
        $this->dispatch('alert', ['type' => 'success',  'message' => 'Logout successfully']);
        return redirect()->to('/');
    }
}
