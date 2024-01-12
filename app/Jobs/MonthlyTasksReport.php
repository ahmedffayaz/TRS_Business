<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\ReportsController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MonthlyTasksReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $start_date = date("Y-m-d", strtotime("first day of this month"));
        $end_date = date("Y-m-d", strtotime("last day of this month"));
        $result = (new ReportsController())->generateReport($start_date, $end_date, 'monthly');
        foreach ($result['users'] as $data) {
            $email = $data['member']['email'];
            $hasPermission = User::where('email', $email)->first()->hasPermissionTo('email_reports');
            if ($hasPermission) {
                if ($data['projects']->count() > 0) {
                    Mail::send('emails.report', $data, function ($message) use ($email) {
                        $message->to($email)->subject('Monthly Report');
                        $message->from(env('MAIL_USERNAME'));
                    });
                }
            }
        }
    }
}
