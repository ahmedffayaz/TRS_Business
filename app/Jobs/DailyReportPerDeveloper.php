<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\ReportsController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DailyReportPerDeveloper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $date = Carbon::yesterday()->format('Y-m-d');
        $result = (new ReportsController())->generateDailyReport($date);
        foreach ($result['users'] as $data) {
            $email = $data['member']['email'];
            $hasPermission = User::where('email', $email)->first()->hasPermissionTo('email_reports');
            if ($hasPermission) {
                if (count($data['data']) > 0) {
                    Mail::send('emails.daily-report', $data, function ($message) use ($email, $date) {
                        $date = Carbon::parse($date)->format('d M y');
                        $message->to($email)->subject('Daily Report of Dev - ' . $date);
                        $message->from(env('MAIL_USERNAME'));
                    });
                }
            }
        }
    }
}
