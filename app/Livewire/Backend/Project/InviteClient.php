<?php

namespace App\Livewire\Backend\Project;

use Livewire\Component;

class InviteClient extends Component
{
    public $showModal = false;
    public $generatedLink;

    protected $listeners = ['openInviteClientModal'];

    public function openInviteClientModal()
    {
        $this->showModal = true;
    }

    public function closeInviteClientModal()
    {
        $this->showModal = false;
    }
    public function render()
    {
        return view('livewire.backend.project.invite-client');
    }
}
