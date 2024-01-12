<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function generateReport($start_date, $end_date = null, $type = 'daily')
    {
        $formatted_date = $this->generateFormattedDate($start_date, $end_date);

        $projects = Project::select('id', 'name', 'end_date')
            ->whereHas('tasks.comments', function ($query) use ($start_date, $end_date, $type) {
                $query->where('comments.type', 'time');
                if (!$end_date) {
                    if ($type == 'daily') {
                        $query->whereBetween('comments.dated', [$start_date, Carbon::createFromFormat('Y-m-d', $start_date)->addDay(1)->format('Y-m-d')])->where('comments.created_at', '>=', $start_date.' 10:00:00')->where('comments.created_at', '<', Carbon::createFromFormat('Y-m-d', $start_date)->addDay(1)->format('Y-m-d').' 10:00:00');
                    } else {
                        $query->whereDate('comments.dated', $start_date);
                    }

                } else {
                    $query->whereBetween('comments.dated', [$start_date, $end_date])
                    ->whereBetween('comments.created_at', [$start_date, $end_date]);
                }
            })->with(['tasks' => function($query){
                $query->whereHas('user', function($query){ $query->select('id','first_name','last_name', 'account_type')->where('account_type', 'active');});
                $query->with('user');
            }, 'tasks.comments' => function ($query) use ($start_date, $end_date) {
            $query->select('comments.id', 'comments.description', 'comments.time', 'comments.task_id', 'comments.from')
                ->where('comments.type', 'time');
            if (!$end_date) {
                $query->whereBetween('comments.dated', [$start_date, Carbon::createFromFormat('Y-m-d', $start_date)->addDay(1)->format('Y-m-d')])->where('comments.created_at', '>=', $start_date.' 10:00:00')->where('comments.created_at', '<', Carbon::createFromFormat('Y-m-d', $start_date)->addDay(1)->format('Y-m-d').' 10:00:00');
            } else {
                $query->whereBetween('comments.dated', [$start_date, $end_date])
                ->whereBetween('comments.created_at', [$start_date, $end_date]);
            }
        }, 'tasks.comments.from_user', 'members' => function ($query) {
            $query->select('users.id', 'first_name', 'last_name', 'email')
                ->with(['roles'])
                ->where('account_type', 'active');
        }])->where('reports_schedule', 'like', '%' . $type . '%')
            ->where('status', '!=', 'delivered')->from('projects')->get();

        $projects = $projects->toArray();
        foreach ($projects as $key => $project) {
            $projects[$key]['tasks'] = array_filter($project['tasks'], function ($task) {
                return count($task['comments']) > 0;
            });
            $projects[$key]['time'] = $this->calculateTime($project['tasks']);
        }
        $projects = collect($projects)->recursive();

        $result = [];
        $data = [];

        foreach ($projects as $project) {
            foreach ($project['members'] as $member) {
                $member_id = $member['id'];
                $data[$member_id]['date'] = $formatted_date;
                $data[$member_id]['member'] = [
                    'name' => ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']),
                    'email' => $member['email'],
                ];
                if (!isset($data[$member_id]['projects'])) {
                    $data[$member_id]['projects'] = [];
                }
                $data[$member_id]['projects'][] = $project;
            }
        }
        $result['data'] = $data;

        $users = User::where('account_type', 'active')->get();

        foreach ($users as $user) {

            $result['users'][] = [
                'date' => $formatted_date,
                'member' => [
                    'name' => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                    'email' => $user->email,
                ],
                'projects' => $projects,
            ];
        }

        return $result;
    }

    private function generateFormattedDate($start_date, $end_date = null)
    {
        $formatted_date = formatDate($start_date, 'l, F j Y');
        if ($end_date) {
            $formatted_date .= ' - ' . formatDate($end_date, 'l, F j Y');
        }
        return $formatted_date;
    }

    private function calculateTime($tasks)
    {
        $time = 0;
        foreach ($tasks as $task) {
            if (count($task['comments']) > 0) {
                $time += array_sum(array_column($task['comments'], 'time'));
            }
        }
        return $time;
    }

    public function generateDailyReport($date)
    {
        $formatted_date = $this->generateFormattedDate($date);
        $result = DB::select("SELECT p.name as project_name, p.id as project_id, t.id as task_id, t.name, t.completed_at, t.end_date, u.`first_name`, u.last_name, c.time, c.description, c.from FROM projects AS p JOIN tasks AS t ON t.`project_id` = p.id JOIN comments AS c ON c.`task_id` = t.id JOIN users AS u ON u.id = c.from WHERE p.`status` != 'delivered' AND p.reports_schedule LIKE '%daily%' AND p.deleted_at IS NULL AND t.deleted_at IS NULL AND c.dated = '" . $date . "' AND c.type = 'time'");
        $data = [];
        foreach ($result as $record) {
            if (isset($data[$record->from])) {
                if (!isset($data[$record->from]['projects'][$record->project_id])) {
                    $data[$record->from]['projects'][$record->project_id] = [
                        'name' => $record->project_name,
                    ];
                }
                if (!isset($data[$record->from]['projects'][$record->project_id]['tasks'][$record->task_id])) {
                    $data[$record->from]['projects'][$record->project_id]['tasks'][$record->task_id] = [
                        'name' => $record->name,
                        'completed_at' => $record->completed_at,
                        'end_date' => $record->end_date,
                    ];
                }
                $data[$record->from]['projects'][$record->project_id]['tasks'][$record->task_id]['comments'][] = [
                    'description' => $record->description,
                    'time' => $record->time,
                ];
            } else {
                $data[$record->from] = [
                    'first_name' => $record->first_name,
                    'last_name' => $record->last_name,
                ];
                $data[$record->from]['projects'][$record->project_id] = [
                    'name' => $record->project_name,
                ];
                $data[$record->from]['projects'][$record->project_id]['tasks'][$record->task_id] = [
                    'name' => $record->name,
                    'completed_at' => $record->completed_at,
                    'end_date' => $record->end_date,
                    'comments' => [
                        [
                            'description' => $record->description,
                            'time' => $record->time,
                        ],
                    ],
                ];
            }
        }

        foreach ($data as $key => $record) {
            foreach ($record['projects'] as $project_key => $project) {
                $data[$key]['projects'][$project_key]['time'] = $this->calculateTime($project['tasks']);
            }
        }

        $users = User::where('account_type', 'active')->get();
        $result = [];
        foreach ($users as $user) {
            $result['users'][] = [
                'date' => $formatted_date,
                'member' => [
                    'name' => ucfirst($user->first_name) . ' ' . ucfirst($user->last_name),
                    'email' => $user->email,
                ],
                'data' => $data,
            ];
        }
        return $result;
    }
}
