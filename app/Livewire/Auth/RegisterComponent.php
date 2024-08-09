<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Enums\User\UserStatus;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Livewire\Forms\registerForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

#[Layout('components.layouts.register-guest')]
class RegisterComponent extends Component
{
    public $client_id;
    public $business_id;
    public $slug;
    Public $logo;
    Public $business_name;
    public $encryption;
    public registerForm $form;
    public function mount($encryption)
    {
        $this->encryption = $encryption;
        $decryption = Crypt::decrypt($encryption);
        $this->client_id = $decryption['client_id'];
        $this->business_id = $decryption['business_id'];
        $this->slug = $decryption['slug'];
        $this->logo = $decryption['logo'];
        $this->business_name = $decryption['business_name'];
    }
    public function submit()
    {
        $this->validate();

       $user = User::create([
            'first_name' => $this->form->first_name,
            'last_name' => $this->form->last_name,
            'business_id' => $this->business_id,
            'client_id' => $this->client_id,
            'email' => $this->form->email,
            'password' => $this->form->password,
            'is_active' => UserStatus::ACTIVE->value,
        ]);

        $clientRoleId = Role::whereBusinessId($this->business_id)->whereName('client')->pluck('id')->toArray();
        if (!empty($clientRoleId)) {
            $user->roles()->sync($clientRoleId);
        } else {
            Log::warning('No client roles found for business ID ' . $this->business_id);
        }

        $projects = Project::where('client_id', $this->client_id)
            ->where('business_id', $this->business_id)->get();
            foreach ($projects as $project) {
                $project->members()->attach($user->id);
            }

        session()->flash('status', 'Registration successful!');

        if ($user) {
            Auth::login($user);
            return redirect()->route('dashboard.home');
        } else {
            return $this->addError('error', 'User creation failed');
        }
    }
    public function redirectToLogin()
    {
        session()->put('swl_key', $this->encryption);
        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.auth.register-component',[
            'logo' => $this->logo,
            'business_name' => $this->business_name,
        ]);
    }
}
