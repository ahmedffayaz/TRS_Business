<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\Task;
use App\Models\User;
use DateTime;
use Exception;
use DatePeriod;
use App\Models\Comment;
use App\Company;
use App\Invoice;
use App\Models\Project;
use DateInterval;
use Carbon\Carbon;
use App\Attachment;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:add_projects'], ['only' => ['create','store']]);
        $this->middleware(['permission:edit_projects'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:view_projects|view_associated_projects'], ['only' => ['index', 'projects', 'show']]);
        $this->middleware(['permission:delete_projects'], ['only' => ['destroy']]);
        $this->middleware(['permission:assign_member'], ['only' => ['assign']]);
        $this->middleware(['permission:remove_member'], ['only' => ['remove']]);
        $this->middleware(['permission:deliver_project'], ['only' => ['deliverProject']]);
        $this->middleware(['permission:view_revenue'], ['only' => ['revenueReport']]);
        $this->middleware(['permission:view_archived'], ['only' => ['archivedProjects', 'archived']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('projects.index');
    }

    public function projects()
    {
        $projects = Project::with(['client_company'])->withCount(['members', 'tasks'])->orderBy('id', 'desc');
        if ($this->auth_user->hasRole('client')) {
            $projects = $projects->where('client_company_id', $this->company->id);
        } else if ($this->auth_user->hasRole('admin')) {
            $projects = $projects->where('company_id', $this->company->id);
        } else if ($this->auth_user->hasPermissionTo('view_associated_projects')){
            $projects = $projects->whereHas('members', function ($query) {
                $query->where('user_id', $this->auth_user->id);
            });
        }
        return DataTables::of($projects)
            ->addColumn('project_name', function ($project) {
                $html = "<div class='d-flex align-items-center'>
                    <div>";
                if ($this->auth_user->hasPermissionTo('view_projects')) {
                    $url = route('projects.show', $project->id);
                } else if ($this->auth_user->hasPermissionTo('view_associated_projects')) {
                    $url = route('projects.show', $project->id);
                } else {
                    $url = 'javascript:void(0)';
                }
                $html .= "<a class='font-weight-bold text-hover-primary' href='{$url}'>{$project->name}</a>";
                if ($project->client_company) {
                    $client_company = $project->client_company;
                    if ($this->auth_user->hasPermissionTo('view_companies')) {
                        $company_url = route('companies.show', $client_company->id);
                    } else {
                        $company_url = 'javascript:void(0)';
                    }
                    $html .= "<p class='mb-0 font-weight-medium'>Client: <a class='text-hover-light-gray' href='{$company_url}'>{$client_company->name}</a></p>";
                }
                $html .= "</div>
                </div>";
                return $html;
            })->editColumn('start_date', function ($project) {
            return formatDate($project->start_date);
        })->editColumn('end_date', function ($project) {
            return formatDate($project->end_date);
        })->editColumn('status', function ($project) {
            return formatProjectStatus($project->status);
        })->editColumn('budget', function ($project) {
            return ($this->auth_user->hasPermissionTo('view_budget') && $project->budget > 0) ? formatCurrency($project->budget, $project->currency) : '-';
        })->editColumn('members_count', function ($project) {
            return wrapWithLabel($project->members_count);
        })->editColumn('tasks_count', function ($project) {
            return wrapWithLabel($project->tasks_count);
        })->addColumn('actions', function ($project) {
            if ($this->auth_user->hasAnyPermission(['edit_projects', 'view_projects', 'delete_projects', 'view_associated_projects'])) {
                $actions = '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
          m-dropdown-toggle="click">
            <a href="javascript:void(0)" class="m-dropdown__toggle">
              <i class="la la-ellipsis-h"></i>
            </a>
            <div class="m-dropdown__wrapper">
              <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                <div class="m-dropdown__inner">
                  <div class="m-dropdown__body">
                    <div class="m-dropdown__content">
                      <ul class="m-nav">';
                if ($this->auth_user->hasPermissionTo('edit_projects') && $project->status !== 'delivered') {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('projects.edit', $project->id) . '">
					        <span class="m-nav__link-text">Edit project</span>
						</a></li>';
                }
                if ($this->auth_user->hasAnyPermission(['view_projects', 'view_associated_projects'])) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('projects.show', $project->id) . '">
							<span class="m-nav__link-text">View project details</span>
						</a></li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_projects')) {
                    $actions .= '<li class="m-nav__item">
                        <a data-table="dt-bs4-projects" class="m-nav__link btn-archive" href="' . route('projects.archive', $project->id) . '">
							<span class="m-nav__link-text">Archive project</span>
						</a></li>';
                }
                $actions .= '</ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            } else {
                $actions = '<button class="btn btn-outline-dark btn-sm"  id="actions" disabled>
                    <i class="la la-lock"></i></button>';
            }
            return $actions;
        })->rawColumns(['actions', 'project_name', 'client_company', 'members_count', 'status', 'tasks_count'])->addIndexColumn()->make(true);
    }

    public function archived()
    {
        return view('projects.archived');
    }

    public function archivedProjects()
    {
        $projects = Project::onlyTrashed()->with(['client_company'])->withCount(['members', 'tasks']);
        if ($this->auth_user->hasRole('client')) {
            $projects = $projects->where('client_company_id', $this->company->id);
        } else if ($this->auth_user->hasPermissionTo('view_projects')) {
            $projects = $projects->where('company_id', $this->company->id);
        } else if ($this->auth_user->hasPermissionTo('view_associated_projects')) {
            $projects = $projects->whereHas('members', function ($query) {
                $query->where('user_id', $this->auth_user->id);
            });
        } else {
            $projects = $projects->whereHas('members', function ($query) {
                $query->where('user_id', $this->auth_user->id);
            });
        }
        return DataTables::of($projects)
            ->addColumn('project_name', function ($project) {
                $html = "<div class='d-flex align-items-center'>
                    <div>";
                if ($this->auth_user->hasPermissionTo('view_projects')) {
                    $url = route('projects.show', $project->id);
                } else {
                    $url = 'javascript:void(0)';
                }
                $html .= "<a class='font-weight-bold text-hover-primary' href='{$url}'>{$project->name}</a>";
                if ($project->client_company) {
                    $client_company = $project->client_company;
                    $company_url = route('companies.show', $client_company->id);
                    $html .= "<p class='mb-0 font-weight-medium'>Client: <a class='text-hover-light-gray' href='{$company_url}'>{$client_company->name}</a></p>";
                }
                $html .= "</div>
                </div>";
                return $html;
            })->editColumn('start_date', function ($project) {
            return formatDate($project->start_date);
        })->editColumn('end_date', function ($project) {
            return formatDate($project->end_date);
        })->editColumn('status', function ($project) {
            return formatProjectStatus($project->status);
        })->editColumn('budget', function ($project) {
            return ($this->auth_user->hasPermissionTo('view_budget') && $project->budget > 0) ? $project->budget . ' ' . currencies($project->currency) : '-';
        })->editColumn('members_count', function ($project) {
            return wrapWithLabel($project->members_count);
        })->editColumn('tasks_count', function ($project) {
            return wrapWithLabel($project->tasks_count);
        })->addColumn('actions', function ($project) {
            if ($this->auth_user->hasAnyPermission(['view_projects', 'delete_projects'])) {
                $actions = '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
          m-dropdown-toggle="click">
            <a href="javascript:void(0)" class="m-dropdown__toggle">
              <i class="la la-ellipsis-h"></i>
            </a>
            <div class="m-dropdown__wrapper">
              <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                <div class="m-dropdown__inner">
                  <div class="m-dropdown__body">
                    <div class="m-dropdown__content">
                      <ul class="m-nav">';
                if ($this->auth_user->hasPermissionTo('delete_projects')) {
                    $keys = json_encode([ 'total' => '#totalProjects', 'active' => '#activeProjects', 'archived' => '#archivedProjects']);
                    $actions .= "<li class='m-nav__item'><a data-table='dt-bs4-projects' class='m-nav__link btn-restore' data-counter-path='" . route("projects.counters") . "' data-keys='" . $keys . "' href='" . route("projects.restore", $project->id) . "'>
							<span class='m-nav__link-text'>Restore</span>
						</a></li><li class='m-nav__item'><a data-table='dt-bs4-projects' class='m-nav__link btn-delete' data-counter-path='" . route("projects.counters") . "' data-keys='" . $keys . "' href='" . route("projects.destroy", $project->id) . "'>
							<span class='m-nav__link-text'>Delete project</span>
						</a></li>";
                }
                $actions .= '</ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            } else {
                $actions = '<button class="btn btn-outline-dark btn-sm"  id="actions" disabled>
                    <i class="la la-lock"></i></button>';
            }
            return $actions;
        })->rawColumns(['actions', 'project_name', 'client_company', 'members_count', 'status', 'tasks_count'])->addIndexColumn()->make(true);
    }

    public function counters()
    {
        $counters = DB::table('projects')->selectRaw('IF(ISNULL(deleted_at), "active", "archived") AS isDeleted, COUNT(*) AS counter')->groupBy('isDeleted')->get();
        $total = 0;
        $active = 0;
        $archived = 0;
        foreach ($counters as $counter) {
            $total += $counter->counter;
            if ($counter->isDeleted === "active") {
                $active = $counter->counter;
            } else {
                $archived = $counter->counter;
            }
        }
        return response()->json([
            'total' => $total,
            'active' => $active,
            'archived' => $archived,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function create()
    {
        $companies = ['' => 'Select Company'] + Company::all()->pluck('name_with_type', 'id')->all();
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'client');
        })->where('account_type', 'active')->get()->pluck('nameWithDesignation', 'id');
        return view('projects.create', compact('companies', 'users'));
    }

    public function projectMembers(int $id)
    {
        try {
            $members = Project::findOrFail($id)->members()
                ->with(['roles'])->where('account_type', 'active')->get();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $members,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => "Project does not exists!",
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProjectRequest $request
     * @param Project $project
     * @return RedirectResponse
     */
    public function store(ProjectRequest $request, Project $project)
    {
        try {
            DB::beginTransaction();
            $inputs = $request->all();
            if (isset($inputs['reports'])) {
                $inputs['reports_schedule'] = implode(",", $inputs['reports']);
            }
            $inputs['company_id'] = auth()->user()->company_id;
            if (!empty($inputs['members'])) {
                if ($inputs['status'] == 'pending') {
                    $inputs['status'] = 'in-progress';
                }
                $project = Project::create($inputs);
                $member = array_diff($inputs['members'], $project->members()->pluck('user_id')->toArray());
                sort($member);
                $project->members()->sync($inputs['members']);
            } else {
                if ($inputs['status'] == 'in-progress') {
                    flash()->error('Incorrect project status! This project is not assigned to anyone. Please make the assignment and then update the status.');
                    return redirect()->route('projects.index');
                } else {
                    $project = Project::create($inputs);
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $mimes = $file->getMimeType();
                    if ($mimes == 'video/quicktime') {
                        $mimes = 'video/mp4';
                    }
                    $file_name = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('public')->putFileAs('/files', $file, $file_name);
                    Attachment::create([
                        'name' => $file->getClientOriginalName(),
                        'mimes' => $mimes,
                        'file' => $path,
                        'attachment_id' => $project->id,
                        'attachment_type' => 'App\Project',
                    ]);
                }
            }

            DB::commit();

            flash()->success('Project created successfully!');
        } catch (Exception $exception) {
            DB::rollBack();
            flash()->error($exception->getMessage());
        }
        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|RedirectResponse|\Illuminate\View\View
     */
    public function show(int $id)
    {
        try {
            $project = Project::where(function ($query) use ($id) {
                if ($this->auth_user->hasPermissionTo('view_associated_projects') && !$this->auth_user->hasRole('admin')) {
                    $query->whereHas('members', function ($query) {
                        $query->where('user_id', $this->auth_user->id);
                    });
                }
            })->with(['company', 'attachments', 'members' => function ($query) {
                $query->where('account_type', 'active');
            }, 'invoices'])->withCount(['members', 'tasks' => function ($query) {
                $query->whereNull('completed_at');
            }])->withTrashed()->findOrFail($id);

            $users = User::whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client');
            })->get()->pluck('nameWithDesignation', 'id');
            $notes = "";
            $invoice = Invoice::whereNotNull('notes')->latest()->first();
            if ($invoice) {
                $notes = $invoice->notes;
            }

            return view('projects.show', compact('project', 'users', 'notes'));
        } catch (ModelNotFoundException $exception) {
            flash()->error('No project found');
            return redirect()->route('projects.index');
        }
    }

    public function revenueReport(int $id)
    {
        try {
            // Project revenue overview
            $project = Project::withTrashed()->with(['members', 'tasks.comments' => function ($query) {
                $query->where('comments.type', 'time');
            }, 'payments', 'members.salaries' => function ($query) use ($id) {
                $query->where('project_id', $id);
            }])->findOrFail($id);
            $cost = [];
            foreach ($project->tasks as $task_index => $task) {
                if (count($task->comments) === 0) {
                    $project->tasks->forget($task_index);
                } else {
                    foreach ($task->comments as $comment) {
                        $member = $project->members->where('id', $comment->from)->first();
                        if (isset($member)) {
                            if (!isset($cost[$member->id])) {
                                $cost[$member->id] = [
                                    'name' => $member->first_name . ' ' . $member->last_name,
                                    'cost' => 0,
                                    'time' => 0,
                                ];
                            }
                            if (count($member->salaries) > 1) {
                                $member->salaries->map(function ($salary) {
                                    if (is_null($salary->end_date)) {
                                        $salary->end_date = Carbon::now()->format('Y-m-d');
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
            $project_total_revenue = formatCurrency($revenue, "PKR");
            $html = "<div>Project Cost: {$project_total_revenue}</div>";
            foreach ($cost as $item) {
                $hours = formatTime($item['time']);
                $member_cost = formatCurrency($item['cost'], "PKR");
                $html .= "<div>{$item['name']} - {$hours} - {$member_cost}</div>";
            }
            $project_cost = array_sum(array_column($cost, 'cost'));
            $project_revenue = $revenue - $project_cost;
            $revenue = formatCurrency($project_revenue, "PKR");
            $class = $project_revenue > 0 ? 'text-success' : 'text-danger';
            $html .= "<div>Revenue: <span class='{$class}'>{$revenue}</span></div>";

            return response()->json([
                'data' => $html,
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'No project found',
            ]);
        }
    }

    /**
     * Retrieve project members
     *
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function members(int $id)
    {
        $project = Project::withTrashed()->find($id);
        $total_hours = 0;
        $members = $project->members()->with(['roles'])->get();
        return DataTables::of($members)
            ->editColumn('email', function ($user) {
                return '<a href="' . route('users.show', $user->id) . '">' . $user->email . '</a>';
            })
            ->editColumn('name', function ($user) {
            return '<div class="kt-widget4__item" style="display: flex;align-items: center">
						<div class="kt-widget4__pic kt-widget4__pic--pic mr-2">
							<span class="avatar pointer" data-toggle="tooltip" title="' . $user->name . '">' . nameToImage($user) . '</span>
						</div>
						<div class="kt-widget4__info">
							<a href="' . route('users.show', $user->id) . '" class="kt-widget4__username">
								' . $user->name . '
							</a>
							<p class="kt-widget4__text mb-0">
								' . implode(', ', $user->getRoleNames()->toArray()) . '
							</p>
						</div>
					</div> 	';
        })->editColumn('hours', function ($user) use ($project, $total_hours) {
            $tests = $project->tasks()->withCount([
                'billable_comments AS hours' => function ($query) use ($user) {
                    $query->select(DB::raw("SUM(time) as hours"))->where('from', $user->id);
                },
            ])->get();
            $total_hours = 0;
            foreach ($tests as $test) {
                $total_hours += $test->hours;
            }
            return formatTime($total_hours);
        })->addColumn('actions', function ($member) use ($id, $project) {
            if ($this->auth_user->hasPermissionTo('remove_member')) {
                if ($project->status != 'delivered') {
                    $actions = '<a data-toggle="tooltip" title="Remove user" data-table="dt-bs4-members" class="btn-remove-member" href="' . route('projects.remove', [$id, $member->id]) . '">
								<i class="la la-trash text-danger"></i>
							</a>';
                } else {
                    $actions = '<button class="btn btn-dark btn-sm"><i class="la la-lock"></i></button>';
                }
                return $actions;
            }
            return '<button class="btn btn-dark btn-sm" disabled><i class="la la-lock"></i></button>';
        })->rawColumns(['name', 'hours', 'email', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Assign members to projects
     *
     * @param int $id
     * @return JsonResponse
     */
    public function assign(int $id)
    {
        $members = request()->get('members');
        try {
            $project = Project::findOrFail($id);
            $new_members = array_diff($members, $project->members()->pluck('user_id')->toArray());
            sort($new_members);
            $project->members()->sync($members);
            if ($project->status !== 'delivered') {
                $project->update(['status' => 'in-progress']);
            }
            if (count($new_members)) {
                //  Notify user
                sendNotification((int) $new_members[0], "You have been assigned to {$project->name}", route('projects.show', $project->id));
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Member assigned successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove member from project
     *
     * @param int $project_id
     * @param int $member_id
     * @return JsonResponse
     */
    public function remove(int $project_id, int $member_id)
    {
        try {
            $project = Project::findOrFail($project_id);
            $project->members()->detach($member_id);
            // Send notification to user
            sendNotification($member_id, "You have been removed from {$project->name}", "");

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Member removed successfully',
                'member_id' => $member_id,
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|RedirectResponse|Response|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        try {
            $project = Project::findOrFail($id);
            // if (!in_array(auth()->user()->id, $project->members->pluck('id')->toArray()) && auth()->user()->roles()->first()->name != "admin") {
            //     abort(401);
            // }
            $companies = ['' => 'Select Company'] + Company::all()->pluck('name_with_type', 'id')->all();
            $users = User::whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client');
            })->where('account_type', 'active')->get()->pluck('nameWithDesignation', 'id');
            return view('projects.edit', compact('companies', 'project', 'users'));
        } catch (ModelNotFoundException $exception) {
            flash()->error('No project found');
            return redirect()->route('projects.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProjectRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(ProjectRequest $request, int $id)
    {
        try {
            DB::beginTransaction();
            $inputs = $request->all();
            if (isset($inputs['reports'])) {
                $inputs['reports_schedule'] = implode(",", $inputs['reports']);
            }

            if (!empty($inputs['members'])) {
                if ($inputs['status'] == 'pending') {
                    $inputs['status'] = 'in-progress';
                }

                $project = Project::findOrFail($id);
                $project->update($inputs);
                $member = array_diff($inputs['members'], $project->members()->pluck('user_id')->toArray());
                sort($member);
                $project->members()->sync($inputs['members']);
            } else {
                if ($inputs['status'] == 'in-progress') {
                    flash()->error('Incorrect project status! This project is not assigned to anyone. Please make the assignment and then update the status.');
                    return redirect()->route('projects.index');
                } else {
                    $project = Project::findOrFail($id);
                $project->update($inputs);
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $mimes = $file->getMimeType();
                    if ($mimes == 'video/quicktime') {
                        $mimes = 'video/mp4';
                    }
                    $file_name = md5(time() . $file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                    $path = Storage::disk('public')->putFileAs('/files', $file, $file_name);
                    Attachment::create([
                        'name' => $file->getClientOriginalName(),
                        'mimes' => $mimes,
                        'file' => $path,
                        'attachment_id' => $project->id,
                        'attachment_type' => 'App\Project',
                    ]);
                }
            }
            DB::commit();

            flash()->success('Project updated successfully');
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            flash()->error($exception->getMessage());
        } catch (Exception $exception) {
            DB::rollBack();
            flash()->error($exception->getMessage());
        }
        return redirect()->route('projects.index');
    }

    public function restore(int $id)
    {
        try {
            $project = Project::onlyTrashed()->findOrFail($id);
            $project->restore();
            $project->update([
                'last_updated_at' => Carbon::now(),
            ]);
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                // Send notification to user
                sendNotification($user->id, "{$project->name} has been restored", route('projects.show', $project->id));
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Project does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Project restored successfully',
        ], JsonResponse::HTTP_OK);
    }

    public function archive(int $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->update([
                'last_updated_at' => Carbon::now(),
            ]);
            $project->delete();
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                // Send notification to user
                sendNotification($user->id, "{$project->name} has been archived", route('projects.archived'));
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Project does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Project archived successfully',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse|Response
     */
    public function destroy(int $id)
    {
        try {
            $project = Project::onlyTrashed()->findOrFail($id);
            // Delete project data first
            $project->members()->detach();
            $project->comments()->delete();
            $project->tasks()->delete();
            // Delete project
            $project->forceDelete();
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Project does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Unable to delete this record, data exists',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Project deleted successfully',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Mark project status as delivered
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deliverProject(int $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->update(['status' => 'delivered']);
            // Archive project
            $project->delete();
            return response()->json([
                'status_code' => JsonResponse::HTTP_OK,
                'message' => 'Project delivered successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Project does not exists!',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function chartData($project_id)
    {
        // Chart data
        $data = Comment::selectRaw('SUM(time) as y, DATE(dated) as x')
            ->whereIn('task_id', function ($query) use ($project_id) {
                $query->select('id')->from(with(new Task)->getTable())
                    ->where('project_id', $project_id);
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
                if ($item) {
                    $formattedData[$key][] = $item->y / 60;
                } else {
                    $formattedData[$key][] = 0;
                }
            }
        }
        return response()->json([
            'status_code' => JsonResponse::HTTP_OK,
            'data' => [
                'chartData' => $formattedData,
                'dates' => $dates,
            ],
        ], JsonResponse::HTTP_OK);
    }
}
