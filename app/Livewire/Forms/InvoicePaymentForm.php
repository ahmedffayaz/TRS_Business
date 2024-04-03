<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoicePaymentForm extends Form
{
    public ?float $amount;
    public ?string $billed_at;
    public ?string $bank;
    public ?float $conversion_rate;
    public ?float $bank_charges;
    public ?string $notes;
    public ?bool $send_email;

    public function rules() : array
    {
        return [
            'amount' => 'required|numeric',
            'billed_at' => 'required|date',
            'bank' => 'required|string',
            'conversion_rate' => 'required|numeric',
            'bank_charges' => 'required|numeric',
            'notes' => 'nullable|string',
            'send_email' => 'nullable|bool'
        ];
    }

    public function validationAttributes() : array
    {
        return [
            'amount' => 'amount',
            'billed_at' => 'payment date',
            'bank' => 'bank',
            'conversion_rate' => 'conversion rate',
            'bank_charges' => 'bank charges',
            'notes' => 'notes',
            'send_email' => 'send email'
        ];
    }
}
