<?php

namespace App\Jobs;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ArchiveProjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
    }

    public function handle()
    {
        $date = Carbon::now()->subDays(30);
        $projects = Project::whereDate('last_updated_at', '<=', $date)->get();
        foreach ($projects as $project) {
            $project->delete();
        }
    }
}
