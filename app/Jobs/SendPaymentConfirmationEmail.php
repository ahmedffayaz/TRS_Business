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
    public function __construct($data, $email)
    {
        $this->data = $data;
        $this->email = $email;
    }

    public function handle()
    {
        $email = $this->email;
        $businessName = $this->data['business_name'];
        Mail::send('emails.invoice-payment', $this->data, function ($message) use ($email, $businessName) {
            $message->from(env('MAIL_USERNAME'), $businessName);
            $message->to($email)->subject('Thanks for Payment');
        });
    }
}
