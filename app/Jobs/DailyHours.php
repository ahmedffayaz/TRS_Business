<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;
use App\Models\Comment;

class DailyHours implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $yesterday = Carbon::yesterday();
        $comments = Comment::select(['time', 'from'])
            ->selectRaw('SUM(time) as time')
            ->groupBy('from')
            ->whereDate('dated', $yesterday)
            ->get()->toArray();
        foreach ($comments as $comment) {
            $timeInHours = formatTime($comment['time']);
            if ($timeInHours < 8) {
                sendNotification((int)$comment['from'], "You have not completed 8 hours on {$yesterday->format('M jS, Y')}", route('tasks.index'));
            }
        }
    }
}
