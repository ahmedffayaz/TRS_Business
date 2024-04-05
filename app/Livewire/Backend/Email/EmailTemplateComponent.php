<?php

namespace App\Livewire\Backend\Email;

use Livewire\Component;
use App\Models\Business;
use App\Models\EmailTemplate;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use App\Livewire\Forms\EmailTemplateForm;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Title('Email Templates')]
class EmailTemplateComponent extends Component
{
    use WithMainModal;

    public $business;
    public EmailTemplateForm $form;

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
        $this->form->isUpdate = true;

        try {
            $email = EmailTemplate::findOrFail($id);
            $this->form->set($email);
            $this->openMainModal();
        } catch (Exception $exception) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            $emailTemplate = EmailTemplate::findOrFail($id);
            DB::beginTransaction();
            $emailTemplate->update([
                'subject' => $validated['subject'],
                'body' => $validated['body']
            ]);
            DB::commit();
            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Email template updated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error on update email template model not found: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on update email template: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function render()
    {
        $emails = $this->getEmails();
        return view('livewire.backend.email.email-template-component', compact('emails'));
    }
}
