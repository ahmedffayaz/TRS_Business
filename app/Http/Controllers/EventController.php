<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class EventController extends Controller
{
    //
    public function events()
    {
        $user = auth()->user();
        $start = Carbon::parse(request()->get('start'))->format('Y-m-d');
        $end = Carbon::parse(request()->get('end'))->format('Y-m-d');
        $events = [];
        if (request('type') == 'task') {
            if ($user->hasRole('admin')) {
                $projects = Project::whereBetween('start_date', [$start, $end])->get();
                $comments = Comment::with('to_user', 'fromUser', 'task')
                    ->whereIn('type', ['assigned', 'removed', 'time'])
                    ->whereBetween('dated', [$start, $end])->get();
            } else {
                $projects = Project::whereHas('members', function (Builder $query) use($user){
                    $query->where('user_id', $user->id);
                })->get();
                $tasks = [];
                foreach ($projects as $project) {
                    $tasks = array_merge($tasks, $project->tasks->pluck('id')->toArray());
                }
                $comments = Comment::with('to_user', 'fromUser', 'task', 'task.project')
                    ->whereHas('task', function (Builder $query) use ($tasks) {
                        $query->whereIn('task_id', $tasks);
                    })->whereIn('type', ['assigned', 'removed', 'time'])
                    ->where('from', $user->id)
                    ->whereBetween('dated', [$start, $end])->get();
            }
            foreach ($projects as $project) {
                $events[] = [
                    'title' => $project->name,
                    'start' => $project->start_date,
                    'end' => $project->start_date,
                    'color' => '#adc3d1',
                    'borderColor' => '#98b0c0',
                    'editable' => false,
                ];
            }
            foreach ($comments as $comment) {
                $events[] = [
                    'title' => $this->generateEventTitle($comment),
                    'start' => date('Y-m-d', strtotime($comment->dated)),
                    'end' => date('Y-m-d', strtotime($comment->dated)),
                    'color' => '#adc3d1',
                    'borderColor' => '#98b0c0',
                    'editable' => false,
                ];
            }
        }

        if (request('type') == 'attendance') {
            $attendance = Leave::with('user')
                ->where('start_date', '>=', $start)
                ->where('end_date', '<=', $end)->get();
            foreach ($attendance as $record) {
                $days = (dateDiff($record->end_date, $record->start_date) + 1);
                $end_date = $record->end_date;
                if ($days > 0) {
                    $end_date = date('Y-m-d', strtotime($record->end_date . ' +1 day'));
                }
                $working_label = $record->is_working == '1' ?
                    'Work from home' : ($record->is_working == '2' ? 'Half Leave' : 'Leave ');
                $events[] = [
                    'title' => $working_label . '<br/>'
                        . $record->user->first_name . ' ' . $record->user->last_name . '<br>Status : ' . $record->status . '<br/>Days: ' . (($days === 0) ? 1 : $days),
                    'start' => $record->start_date,
                    'end' => $end_date,
                    'editable' => true,
                    'user_id' => $record->user->id,
                    'auth_user_id' => $user->id,
                    'color' => $record->is_working == '1' ? '#38c172' : '#e3342f',
                    'borderColor' => $record->is_working == '1' ? 'green' : 'red',
                    'evt' => $record,
                    'user_role' => $this->auth_user->hasPermissionTo('delete_leaves'),
                ];
            }

        }

        return response()->json($events, 200);
    }

    private function generateEventTitle($comment)
    {
        if ($comment->type === 'assigned') {
            return '<div class="d-flex justify-content-between align-items-center">
                        <div class="calendar-avatar" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $comment->to_user->name . '">' . getInitials($comment->to_user) . '</div>
                        <span class="badge bg-success">assigned</span>
                    </div><br/>' . optional($comment->task)->name;
        } else if ($comment->type === 'removed') {
            return '<div class="d-flex justify-content-between align-items-center">
                        <div class="calendar-avatar" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $comment->to_user->name . '">' . getInitials($comment->to_user) . '</div>
                        <span class="badge bg-danger">removed</span>
                    </div><br/>' . optional($comment->task)->name;
        } else {
            return '<div class="event-title"><div class="d-flex justify-content-between align-items-center">
                        <div class="calendar-avatar bg-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $comment->fromUser->name . '">' . getInitials($comment->fromUser) . '</div>
                        <div class="calendar-avatar" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $comment->task->project->name . '">' . getInitials($comment->task->project->name) . '</div>
                        <span class="badge bg-success">' . formatTime($comment->time) . '</span>
                    </div>
                    <span class="fc-description text-wrap d-none"><br />' . $comment->description . '</span></div>';
        }
    }

}
