<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class LeaveRequestNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $leave = null;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function handle()
    {
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $email = $admin->email;
            $name = $this->leave->user->first_name . ' ' . $this->leave->user->last_name;
            Mail::send('emails.leave_notification', [
                'name' => $name,
                'from' => $this->leave->start_date,
                'to' => $this->leave->end_date,
                'reason' => $this->leave->reason
            ], function ($message) use ($email, $name) {
                $message->to($email)->subject($name . ' Leave Request');
            });
            
        }
    }
}