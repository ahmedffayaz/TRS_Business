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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

#[Layout('components.layouts.guest')]
class RegisterComponent extends Component
{
    public $client_id;
    public $business_id;
    public $slug;
    public registerForm $form;
    public function mount($encryption)
    {
        $decryption = Crypt::decrypt($encryption);
        $this->client_id = $decryption['client_id'];
        $this->business_id = $decryption['business_id'];
        $this->slug = $decryption['slug'];
    }
    public function submit()
    {
        $this->validate();

       $user = User::create([
            'first_name' => $this->form->first_name,
            'last_name' => $this->form->last_name,
            'phone' => $this->form->phone,
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

        $project = Project::where('slug', $this->slug)->firstOrFail();
        $project->members()->attach($user->id);

        session()->flash('status', 'Registration successful!');
        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.auth.register-component');
    }
}
