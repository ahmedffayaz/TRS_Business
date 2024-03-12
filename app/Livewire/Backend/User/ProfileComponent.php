<?php

namespace App\Livewire\Backend\User;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('User Detail')]
class ProfileComponent extends Component
{
    public $user, $id;

    public function render()
    {
        $this->user = User::with(['roles', 'projects', 'client' => function ($query) {
            $query->with(['business']);
        }])->findOrFail($this->id);
        return view('livewire.backend.user.profile-component');
    }
}
