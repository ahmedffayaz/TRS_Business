<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;

use Livewire\Component;

class ForgotPasswordComponent extends Component
{
    public $email;
    protected $rules = [
        'email' => 'required|email',
    ];

    #[Layout('components.layouts.guest')]
    public function render()
    {
        return view('livewire.auth.forgot-password-component');
    }

    public function submit()
    {
        $this->validate();
        $broker = Password::broker();
        $response = $broker->sendResetLink(['email' => $this->email]);

        if ($response === Password::RESET_LINK_SENT) {
            $this->dispatch('alert', ['type' => 'success',  'message' => 'A password reset link has been sent to your email address.']);
            session()->flash('status', 'A password reset link has been sent to your email address.');
        } else {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Unable to send password reset link. Please try again later.']);
            session()->flash('error', 'Unable to send password reset link. Please try again later.');
        }
    }
}
