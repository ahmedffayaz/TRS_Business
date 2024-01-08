<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\ReportsController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DailyTasksReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        try {
            $date = Carbon::yesterday()->format('Y-m-d');
            $result = (new ReportsController())->generateReport($date, null, 'daily');
            foreach ($result['users'] as $data) {
                $email = $data['member']['email'];
                $hasPermission = User::where('email', $email)->first()->hasPermissionTo('email_reports');
                if ($hasPermission) {
                    if ($data['projects']->count() > 0) {
                        Mail::send('emails.report', $data, function ($message) use ($email, $date) {
                            $date = Carbon::parse($date)->format('d M y');
                            $message->to($email)->subject('Daily Report - ' . $date);
                            $message->from(env('MAIL_USERNAME'));
                        });
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
