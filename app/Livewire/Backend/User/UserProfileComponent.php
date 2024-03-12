<?php

namespace App\Livewire\Backend\User;

use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Libraries\ImageManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Livewire\Forms\UserProfileForm;
use Illuminate\Contracts\Auth\Authenticatable;

#[Title('User Profile')]
class UserProfileComponent extends Component
{
    use WithFileUploads;

    public UserProfileForm $form;

    public $profileLogo;
    public string $defaultImage = 'assets/images/avatar.png';
    public string $imagePath = 'profile';

    public function mount(Authenticatable $user)
    {
        $this->form->set($user);
        $this->profileLogo = $user->avatar;
    }

    public function render()
    {
        return view('livewire.backend.user.user-profile-component');
    }

    public function update(Authenticatable $user)
    {
        $this->form->id = $user->id;
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            if (!empty($validated['avatar'])) {
                $imageManager = new ImageManager();
                $validated['avatar'] = 'profile/' . $imageManager->setFile($validated['avatar'])->resize(64)->setDirectory($this->imagePath)->save();
            }
            $user->update($validated);
            DB::commit();

            $this->resetValidation();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User profile updated successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on updating user profile:' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
