<?php

namespace App\Livewire\Forms;

use App\Models\EmailTemplate;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EmailTemplateForm extends Form
{
    public $id;
    public ?bool $isUpdate = false;
    public ?string $subject;
    public ?string $tags;
    public ?string $body;

    public function rules() : array
    {
        if ($this->isUpdate) {
            return [
                'subject' => 'required|string|max:191',
                'body' => 'required|string'
            ];
        }
    }

    public function set(EmailTemplate $email): void
    {
        $this->id = $email?->id;
        $this->subject = $email?->subject;
        $this->tags = $email?->keywords;
        $this->body = $email?->body;
    }
}
