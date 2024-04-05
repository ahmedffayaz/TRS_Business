<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $email;
    protected $filteredKeywords;
    protected $filteredKeywordsValue;
    public function __construct($data, $email, $filteredKeywords, $filteredKeywordsValue)
    {
        $this->data = $data;
        $this->email = $email;
        $this->filteredKeywords = $filteredKeywords;
        $this->filteredKeywordsValue = $filteredKeywordsValue;
    }

    public function handle()
    {
        $emailTemplate = emailTemplate('client_invoice_payment_confirm', $this->data, $this->filteredKeywords, $this->filteredKeywordsValue);

        $emailData = array(
            'name' =>  $emailTemplate['title'],
            'email' => $this->email,
            'emailMessage' => $emailTemplate['message'],
            'subject' => $emailTemplate['subject'],
            'businessLogo' => $this->data['business_logo']
        );

        $email = $this->email;
        $businessName = $this->data['business_name'];
        Mail::send('emails.email-template', $emailData, function ($message) use ($emailData, $email, $businessName) {
            $message->from(env('MAIL_USERNAME'), $businessName);
            $message->to($email)->subject($emailData['subject']);
        });
    }
}
