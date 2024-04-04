<?php

namespace App\Livewire\Backend\Email;

use App\Models\Business;
use App\Models\EmailTemplate;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Email Templates')]
class EmailTemplateComponent extends Component
{
    use WithMainModal;

    public $business;

    public function mount()
    {
        $this->business = Business::whereName(session('business'))->first();
    }

    private function getEmails()
    {
        return EmailTemplate::get();
    }

    public function edit($id)
    {
        $this->openMainModal();
    }

    public function render()
    {
        $emails = $this->getEmails();
        return view('livewire.backend.email.email-template-component', compact('emails'));
    }
}
