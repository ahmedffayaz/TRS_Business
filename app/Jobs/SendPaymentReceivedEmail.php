<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentReceivedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $users;

    public function __construct($data, $users)
    {
        $this->data = $data;
        $this->users = $users;
    }

    public function handle()
    {
        foreach ($this->users as $user) {
            $this->data['first_name'] = $user->first_name;
            $businessName = $this->data['business_name'];
            Mail::send('emails.payment-received', $this->data, function ($message) use ($user, $businessName) {
                $message->from(env('MAIL_USERNAME'), $businessName);
                $message->to($user->email)->subject('Payment Received');
            });
        }
    }
}
