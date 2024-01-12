<?php

namespace App\Livewire\Partials;

use Exception;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

class FileUploadComponent extends Component
{
    use WithFileUploads;
    public $avatar;
    #[Validate('image|max:1024')] // 1MB Max
    public $avatarFile;

    
    public function mount(\Illuminate\Contracts\Auth\Authenticatable $user)
    {
        $avatar = $user->avatar ? Storage::url($user->avatar) : '/assets/images/avatar.png';
        $this->avatar = $avatar;
    }

    public function render()
    {
        return view('livewire.partials.file-upload-component');
    }

     public function uploadAvatarFile()
    {
        dd($this->avatarFile);
        $this->avatarFile->store('photos');
        // $user = auth()->user();
        // $dataValid = $this->validate([
        //     'avatarFile' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        // ]);
        // try {
        //     $filenameWithExt = $dataValid['avatarFile']->getClientOriginalName();
        //     $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        //     $extension = $dataValid['avatarFile']->getClientOriginalExtension();
        //     $fileNameToStore = $filename . '_' . time() . '.' . $extension;
        //     $this->avatarFile->store('profile', $fileNameToStore);
        //     $this->avatar = 'profile' . '/' . $fileNameToStore;

        //     // $user->update([
        //     //     'avatar' => $fileNameToStore,
        //     // ]);
        //     $this->dispatch('alert', ['type' => 'success', 'message' => 'Avatar uploaded successfully']);
        // } catch (Exception $exception) {
        //     $this->dispatch('alert', ['type' => 'error', 'message' => 'Error uploading avatar']);
        // }
    }
}
