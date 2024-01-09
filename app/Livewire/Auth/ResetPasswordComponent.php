<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Illuminate\Http\Request;
use Livewire\Component;
use Illuminate\Support\Str;

class ResetPasswordComponent extends Component
{
    public $email;
    public $password;
    public $password_confirmation;
    public $token;
    protected $rules = [
        'email' => 'required|email',
        'token' => 'required',
        'password' => ['required', 'confirmed', 'min:7'],
    ];

    public function mount(Request $request)
    {
        $this->token = $request->route('token');
        $this->email = $request->email;
    }

    #[Layout('components.layouts.guest')]
    public function render()
    {
        return view('livewire.auth.reset-password-component');
    }

    public function submit()
    {
        $this->validate();
        $status = Password::reset(
            ['email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'token' => $this->token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            session()->flash('status', 'Password has been reset successfully.');
        } else {
            session()->flash('error', trans($status));
        }
    }
}
