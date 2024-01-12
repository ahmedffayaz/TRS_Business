<?php

namespace App\Console;

use App\Jobs\ArchiveProjects;
use App\Jobs\DailyReportPerDeveloper;
use App\Jobs\DailyTasksReport;
use App\Jobs\MonthlyTasksReport;
use App\Jobs\WeeklyTasksReport;
use App\Jobs\DailyHours;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new DailyReportPerDeveloper())->dailyAt('10:00');
        $schedule->job(new DailyTasksReport())->dailyAt('10:00');
        $schedule->job(new WeeklyTasksReport())->weekly();
        $schedule->job(new MonthlyTasksReport())->monthly();
        $schedule->job(new ArchiveProjects())->dailyAt('23:59');
        $schedule->job(new DailyHours())->dailyAt('10:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
