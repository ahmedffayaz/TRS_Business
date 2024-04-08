<?php

namespace App\Livewire\Backend\Project;

use DateTime;
use Exception;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Project;
use Livewire\Component;
use App\Traits\WithMainModal;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Title;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Title('Project Details')]
class ProjectDetailComponent extends Component
{
    use WithMainModal;

    public string $slug;
    public bool $isRevenueModalOpen = false;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    private function getProject()
    {
        return Project::sessionBusiness()->whereSlug($this->slug)
            ->with(['tasks', 'client', 'members' => function ($query) {
                $query->with(['roles']);
            }, 'invoices'])->withCount(['tasks', 'members'])->firstOrFail();
    }

    public function render()
    {
        $project = $this->getProject();
        return view('livewire.backend.project.project-detail-component', compact('project'));
    }

    public function showRevenueModal()
    {
        try {
            $slug = $this->slug;
            $project = Project::sessionBusiness()->whereSlug($slug)->withTrashed()
            ->with(['members', 'tasks.comments' => function ($query) {
                $query->where('comments.type', 'time');
            }, 'payments', 'members.salaries' => function ($query) use ($slug) {
                $query->whereHas('project', function ($query) use ($slug) {
                    $query->whereSlug($slug);
                });
            }, 'business'])->firstOrFail();
            $cost = [];

            foreach($project->tasks as $taskIndex => $task) {
                if (count($task->comments) === 0) {
                    $project->tasks->forget($taskIndex);
                } else {
                    foreach($task->comments as $comment) {
                        $member = $project->members->where('id', $comment->from)->first();
                        if (isset($member)) {
                            if (!isset($cost[$member->id])) {
                                $cost[$member->id] = [
                                    'name' => $member->first_name . ' ' . $member->last_name,
                                    'cost' => 0,
                                    'time' => 0
                                ];
                            }

                            if (count($member->salaries) > 1) {
                                $member->salaries->map(function ($salary) use ($project) {
                                    if (is_null($salary->end_date)) {
                                        $salary->end_date = Carbon::now()->format($project->business->date_format);
                                    }
                                    return $salary;
                                });

                                $salary = $member->salaries->where('start_date', '<=', $comment->dated)
                                    ->where('end_date', '>=', $comment->dated)->first();

                                if ($salary) {
                                    $cost[$member->id]['cost'] += floatval($salary->salary_per_hour) * ($comment->time / 60);
                                    $cost[$member->id]['time'] += $comment->time;
                                }
                            } else if (count($member->salaries) == 1) {
                                $cost[$member->id]['cost'] += floatval($member->salaries->first()->salary_per_hour) * ($comment->time / 60);
                                $cost[$member->id]['time'] += $comment->time;
                            }
                        }
                    }
                }
            }

            $revenue = 0;
            foreach ($project->payments as $payment) {
                $revenue += ($payment->amount - $payment->bank_charges) * $payment->conversion_rate;
            }

            sort($cost);
            $projectTotalRevenue = formatCurrency($revenue, "PKR");

            $data = "<div>Project Cost: {$projectTotalRevenue}</div>";

            foreach ($cost as $item) {
                $hours = formatTime($item['time']);
                $member_cost = formatCurrency($item['cost'], "PKR");
                $data .= "<div>{$item['name']} - {$hours} - {$member_cost}</div>";
            }

            $projectCost = array_sum(array_column($cost, 'cost'));
            $projectRevenue = $revenue - $projectCost;
            $revenue = formatCurrency($projectRevenue, "PKR");
            $class = $projectRevenue > 0 ? 'text-success' : 'text-danger';
            $data .= "<div>Revenue: <span class='{$class}'>{$revenue}</span></div>";

            $this->dispatch('open-revenue-modal', ['projectRevenueDetail' => $data]);
        } catch (Exception $exception) {
            Log::error('Get error while get project revenue: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function chartData($slug)
    {
        try {
            $project = Project::whereSlug($slug)->firstOrFail();
            $projectId = $project?->id;
            // Chart data
            $data = Comment::selectRaw('SUM(time) as y, DATE(dated) as x')
                ->whereIn('task_id', function ($query) use ($projectId) {
                    $query->select('id')->from(with(new Task)->getTable())
                        ->where('project_id', $projectId);
                })->where('type', 'time')
                ->groupBy(DB::raw('x'))->orderBy('x', 'ASC')->get();

            $dates = [];
            $formattedData = [];
            if (count($data) > 0) {
                $period = new DatePeriod(new DateTime($data[0]->x), new DateInterval('P1D'), new DateTime($data[count($data) - 1]->x . ' +1 day'));
                foreach ($period as $key => $date) {
                    $formattedDate = $date->format("Y-m-d");
                    $dates[] = formatDate($formattedDate, 'D, d M y');
                    $item = $data->where('x', $formattedDate)->first();
                    $formattedData[$key][] = formatDate($formattedDate);

                    $formattedData[$key][] = $item ? $item->y / 60 : 0;
                }
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => [
                    'chartData' => $formattedData,
                    'dates' => $dates,
                ],
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error on chart data model note found: ' . $exception->getMessage());
        } catch (Exception $exception) {
            Log::error('Get error on chart data: ' . $exception->getMessage());
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
