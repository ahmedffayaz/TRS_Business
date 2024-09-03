<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Leave;
use App\Models\Comment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\Comment\CommentType;
use App\Enums\Leave\LeaveType;

class EventController extends Controller
{
    public function events()
    {
        $user = Auth::user();
        $start =formatDate(request()->input('start'),'Y-m-d');
        $end = formatDate(request()->input('end'),'Y-m-d');
        // $start = Carbon::parse(request()->get('start'))->format('Y-m-d');
        // $end = Carbon::parse(request()->get('end'))->format('Y-m-d');
        $events = [];
        $types = explode(',', request('types'));

        if ($user->hasRole('admin')) {
            $projects = Project::whereBetween('start_date', [$start, $end])->get();
            $comments = Comment::with('to_user', 'fromUser', 'task')
                ->whereIn('type', [CommentType::ASSIGNED,  CommentType::REMOVED,CommentType::TIME])
                ->whereBetween('dated', [$start, $end])->get();
        } else {
            $projects = Project::whereHas('members', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();


            $tasks = [];

            foreach ($projects as $project) {
                $tasks = array_merge($tasks, $project->tasks->pluck('id')->toArray());
            }

            $comments = Comment::with('to_user', 'fromUser', 'task', 'task.project')
            ->whereHas('task', function (Builder $query) use ($tasks) {
                $query->whereIn('task_id', $tasks);
            })
            ->whereIn('type', ['assigned', 'removed', 'time'])
            ->whereBetween('dated', [$start, $end])
            ->when(!$user->hasPermissionTo('view_tasks_in_calendar'), function ($query) use ($user) {
                $query->where('from', $user->id);
            })
            ->get();
        }

        if (in_array('projects', $types)) {
            foreach ($projects as $project) {
                $events[] = [
                    'title' => "<span data-bs-toggle='tooltip' data-bs-placement='top' title='Project deadline'>{$project->name}</span>",
                    'start' => $project->end_date,
                    'end' => $project->end_date,
                    'textColor' => '#7367F0',
                    'borderColor' => 'rgba(115, 103, 240, .12)',
                    'backgroundColor' => 'rgba(115, 103, 240, .12)',
                    'editable' => false,
                ];
            }
        }

        if (in_array('tasks', $types)) {
            foreach ($comments as $comment) {
                $events[] = [
                    'title' => $this->generateEventTitle($comment),
                    'start' => date('Y-m-d', strtotime($comment->dated)),
                    'end' => date('Y-m-d', strtotime($comment->dated)),
                    'textColor' => '#28C76F',
                    'borderColor' => 'rgba(40, 199, 111, .12)',
                    'backgroundColor' => 'rgba(40, 199, 111, .12)',
                    'editable' => false,
                ];
            }
        }


        if (in_array('attendance', $types)) {

            $attendance = Leave::with('user')
                ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                    if ($user->hasPermissionTo('view_leaves')) {
                        $query->whereHas('user', function (Builder $query) {
                            $query->where('id', auth()->user()->id);
                        });
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                })->where('start_date', '>=', $start)->where('end_date', '<=', $end)->get();

            foreach ($attendance as $record) {

                $days = Carbon::parse($record->end_date)->diffInDays(Carbon::parse($record->start_date)) + 1;
                $end_date = $record->end_date;

                if ($days > 0) {
                    $end_date = date('Y-m-d', strtotime($record->end_date . ' +1 day'));
                }
                $working_label = match ($record->is_working) {
                    '1' => ucwords(LeaveType::WORK_FROM_HOME->value),
                    '2' => ucwords(LeaveType::HALF_LEAV->value),
                    default => ucwords(LeaveType::LEAVE->value),
                };

                $events[] = [
                    'title' => $working_label . '<br/>' . $record->user->name . '<br>Status : ' . $record->status->value . '<br/>Days: ' . (($days === 0) ? 1 : $days),
                    'start' => $record->start_date,
                    'end' => $end_date,
                    'can_edit' => $user->hasPermissionTo('edit_leaves'),
                    'user_id' => $record->user->id,
                    'auth_user_id' => $user->id,
                    'textColor' => $record->is_working == '1' ? '#FF9F43' : '#EA5455',
                    'borderColor' => $record->is_working == '1' ? 'rgba(255, 159, 67, .12)' : 'rgba(234, 84, 85, .12)',
                    'backgroundColor' => $record->is_working == '1' ? 'rgba(255, 159, 67, .12)' : 'rgba(234, 84, 85, .12)',
                    'evt' => $record,
                    'can_delete' => $user->hasPermissionTo('delete_leaves'),
                ];
            }
        }

        return response()->json($events, 200);
    }

    private function generateEventTitle($comment)
    {
        if ($comment->type === CommentType::ASSIGNED->value) {
            return '<div class="d-flex justify-content-between align-items-center">
                        <div class="calendar-avatar" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $comment->to_user->name . '">' . getInitials($comment->to_user) . '</div>
                        <span class="badge bg-success">assigned</span>
                    </div><br/>' . optional($comment->task)->name;
        } else if ($comment->type === CommentType::REMOVED->value) {
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
