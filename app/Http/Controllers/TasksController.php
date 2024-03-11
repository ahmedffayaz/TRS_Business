<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Models\Comment;
use App\Events\Message;
use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Storage;
use Yajra\DataTables\Facades\DataTables;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if (!$this->auth_user->hasPermissionTo('view_tasks')){
            return abort(401);
        }
        $start = $request->has('start') ? $request->get('start') : '';
        $end = $request->has('end') ? $request->get('end') : '';
        $keyword = $request->has('keyword') ? $request->get('keyword') : '';
        $hours = $request->has('hours') ? $request->get('hours') : '';
        if($this->auth_user->hasRole('admin')){
            $projects = ['' => 'Select Project'] + Project::pluck('name', 'id')->all();
        }
        else {
            if ($this->auth_user->hasPermissionTo('view_projects')) {
                $projects = ['' => 'Select Project'] + Project::pluck('name', 'id')->all();
            } else if ($this->auth_user->hasPermissionTo('view_associated_projects')) {
                $projects = ['' => 'Select Project'] + Project::whereHas('members', function($query)
                {
                  $query->where('user_id','=', auth()->user()->id);
                 })->pluck('name', 'id')->all();
            }
        }
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'client');
        })->get()->pluck('nameWithDesignation', 'id');

        try {

            $query = Task::withCount(['attachments', 'billable_comments', 'comments_with_time'])
                ->leftJoin('users', 'users.id', '=', 'tasks.user_id')
                ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')->orderBy('id', 'desc');

            if ($request->has('project_id')) {
                $query->where('project_id', $request->get('project_id'));
            } else {
                if ($this->auth_user->hasRole('client')) {
                    $query->where('projects.client_company_id', $this->auth_user->company_id);
                } else if ($this->auth_user->hasRole('team-lead')) {
                    $query->whereIn('projects.id', $this->auth_user->projects()->pluck('projects.id'));
                }
            }
            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('tasks.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('projects.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('users.first_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('users.last_name', 'LIKE', '%' . $keyword . '%');
                });
            }
            if (!empty($hours)) {
                $query->where('comments.time', '>=', $hours * 60);
            }
            if (!empty($start) && !empty($end)) {
                if ($start <= $end) {
                    $query->whereDate('tasks.created_at', '>=', $start)
                        ->whereDate('tasks.created_at', '<=', $end);
                }
            } else if (!empty($start)) {
                $query->whereDate('tasks.created_at', '>=', $start);
            } else if (!empty($end)) {
                $query->whereDate('tasks.created_at', '<=', $end);
            }
            $tasks = $query->orderBy('tasks.completed_at')
                ->orderBy('tasks.priority', 'desc')
                ->orderBy('tasks.end_date')->paginate(15);
             return view('tasks.index', compact('projects', 'users', 'tasks', 'start', 'end', 'keyword', 'hours'));
        }
        catch (Exception $exception) {
            flash()->error($exception->getMessage());
            return redirect()->route('dashboard.home');
        }

    }

    /**
     * Return tasks list
     *
     * @param int|null $project_id
     * @return mixed
     * @throws Exception
     */
    public function tasks(Request $request, int $project_id = null)
    {

        if ($project_id) {
            $project = Project::withTrashed()->find($project_id);

            $tasks = $project->tasks()->with(['user', 'project'])->withCount(['attachments', 'billable_comments', 'comments_with_time']);
        } else {
            $tasks = Task::with(['user', 'project'])
                ->whereHas('project', function ($query) {
                    if ($this->auth_user->hasRole('client')) {
                        $query->whereClientCompanyId($this->auth_user->company_id);
                    } else if ($this->auth_user->hasRole('team-lead')) {
                        $query->whereIn('id', $this->auth_user->projects()->pluck('projects.id'));
                    }

                })->withCount(['attachments', 'billable_comments', 'comments_with_time']);
            if ($this->auth_user->hasRole('developer')) {
                $tasks->where('user_id', $this->auth_user->id);
            }

        }
        return DataTables::of($tasks)
            ->addColumn('select', function ($task) {
                if ($task->billable_comments_count) {
                    return '<div style="position:relative;height:20px;"><label style="position: absolute;top:0;margin-top:0;" class="m-checkbox"><input type="checkbox" class="toggle-invoice" name="tasks[]" value="' . $task->id . '"/><span></span></label></div>';
                } else {
                    return '';
                }
            })->addColumn('project_name', function ($task) {
            return optional($task->project)->name;
        })->editColumn('priority', function ($task) {
            return priorityToNum($task->priority);
        })->editColumn('name', function ($task) {
            $attachment = '';
            if ($task->attachments_count > 0) {
                $attachment = '<i class="la la-paperclip"></i>&nbsp;';
            }
            return $attachment . '<a href="' . route('tasks.show', $task->id) . '">' . $task->name . '</a>';
        })->addColumn('time_spend', function ($task) {
            return formatTime($task->comments()->sum('time'));
        })->editColumn('end_date', function ($task) {
            return formatDate($task->end_date);
        })->editColumn('user', function ($task) {
            return ($task->user) ? '<span class="avatar pointer" data-toggle="tooltip" title="' . $task->user->name . '">' . nameToImage($task->user) . '</span>' : '';
        })->addColumn('status', function ($task) {
            $html = '';
            if (!empty($task->completed_at)) {
                $html = '<i class="la la-check-circle text-green"></i>';
            }
            return $html;
        })->addColumn('actions', function ($task) {
            if ($this->auth_user->hasAnyPermission(['edit_tasks', 'view_tasks', 'delete_tasks', 'add_comments', 'mark_completed'])) {
                $actions = '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="click">
                        <a href="javascript:void(0)" class="m-dropdown__toggle">
                          <i class="la la-ellipsis-h"></i>
                        </a>
            <div class="m-dropdown__wrapper">
              <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                <div class="m-dropdown__inner">
                  <div class="m-dropdown__body">
                    <div class="m-dropdown__content">
                      <ul class="m-nav">';
                if ($this->auth_user->hasPermissionTo('edit_tasks') && empty($task->completed_at)) {
                    $actions .= '<li class="m-nav__item"><a data-id="' . $task->id . '" class="m-nav__link btn-edit-task"
                                    data-url="' . route('tasks.edit', $task->id) . '" href="javascript:void(0)">
                                    <span class="m-nav__link-text">Edit task</span>
                                </a></li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_tasks') && empty($task->user_id) && empty($task->completed_at)) {
                    $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-tasks" class="m-nav__link btn-delete" href="' . route('tasks.destroy', $task->id) . '">
                                    <span class="m-nav__link-text">Delete task</span>
                                </a></li>';
                }
                if ($this->auth_user->hasPermissionTo('view_tasks')) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('tasks.show', $task->id) . '">
                                <span class="m-nav__link-text">View task details</span>
                            </a></li>';
                }
                if ($this->auth_user->hasPermissionTo('add_comments') && is_null($task->project->deleted_at)) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('tasks.chat', $task->id) . '">
                                <span class="m-nav__link-text">Comment</span>
                            </a></li>';
                }
                if ($this->auth_user->hasPermissionTo('mark_completed') && empty($task->completed_at) && $task->comments_with_time_count) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link btn-mark-completed" data-route="' . route('tasks.complete', $task->id) . '" href="javascript:void(0)"><span class="m-nav__link-text">Mark complete</span></a></li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_tasks')) {
                    $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-tasks" class="m-nav__link btn-archive" href="' . route('task.archive', $task->id) . '">
                                <span class="m-nav__link-text">Archive task</span>
                            </a></li>';
                }
                $actions .= '</ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            } else {
                $actions = '<button class="btn btn-outline-dark btn-sm" id="actions" disabled>
                    <i class="la la-lock"></i></button>';
            }
            return $actions;
        })->rawColumns(['select', 'priority', 'name', 'user', 'status', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function store(Request $request, Task $task)
    {
        // return $request;
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'nullable',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required',
            'project_id' => $request->input('task_id') ? 'nullable' : 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();

            $action = 'created';
            $inputs = $request->all();
            $old_user_id = null;

            if (!empty($inputs['task_id'])) {
                $task = Task::findOrFail($inputs['task_id']);
                $old_user_id = $task->user_id;
                $task->update($inputs);
                $task->project()->update([
                    'last_updated_at' => Carbon::now(),
                ]);
                $action = 'updated';
            } else {
                $task = $task->create($inputs);
                $task->project()->update([
                    'status' => 'in-progress',
                    'last_updated_at' => Carbon::now(),
                ]);
            }

            if (!empty($inputs['user_id'])) {
                if ($inputs['user_id'] != $old_user_id) {
                    $payload = [
                        'description' => 'Task assigned',
                        'time' => '',
                        'type' => 'assigned',
                        'to' => (int) $inputs['user_id'],
                        'from' => auth()->user()->id,
                        'task_id' => $task->id,
                    ];
                    Comment::create($payload);
                    event(new Message($payload));
                    if (!empty($old_user_id)) {
                        $payload = [
                            'description' => 'Removed from task',
                            'time' => '',
                            'type' => 'removed',
                            'to' => (int) $old_user_id,
                            'from' => auth()->user()->id,
                            'task_id' => $task->id,
                        ];
                        Comment::create($payload);
                        event(new Message($payload));
                    }
                }
            }

            DB::commit();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => "Task {$action} successfully!",
                'task_id' => $task->id,
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|View
     */
    public function show(int $id)
    {
        try {
            if (!$this->auth_user->hasPermissionTo('view_tasks')) {
                return abort(401);
            }
                $task = Task::with(['project', 'user', 'attachments'])->findOrFail($id);
                $messages = $task->comments()->with([
                    'from_user' => function ($query) {
                        $query->select(['first_name', 'last_name', 'id'])
                        ->with(['roles' => function ($query) {
                            $query->select('title');
                        }]);
                    },
                    'to_user' => function ($query) {
                        $query->select(['first_name', 'last_name', 'id'])
                        ->with(['roles' => function ($query) {
                            $query->select('title');
                        }]);
                    },
                    'attachment',
                    ])->get();
                $members = User::join('comments', function ($join) {
                    $join->on('users.id', '=', 'comments.from')->orOn('users.id', '=', 'comments.to');
                })->groupBy('users.id')->where('comments.task_id', $task->id)
                    ->select('users.*')->get();
                $time_spent = formatTime($task->comments()->sum('time'));
                return view('tasks.show', compact('task', 'time_spent', 'messages', 'members'));
        } catch (ModelNotFoundException $exception) {
            flash()->error($exception->getMessage());
            return redirect()->route('projects.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id)
    {
        try {
            $task = Task::with(['project.members.roles'])->findOrFail($id);
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $task,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            // Delete task comments
            $task->comments()->delete();
            $task->forceDelete();
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Task does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Task deleted successfully',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Return chat messages
     *
     * @param int $task_id
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function chat(int $task_id)
    {
        try {
            $task = Task::with(['project'])->findOrFail($task_id);
            $messages = $task->comments()->with([
                'from_user' => function ($query) {
                    $query->select('first_name', 'last_name', 'id')
                    ->with(['roles' => function ($query) {
                        $query->select('title');
                    }]);
                },
                'to_user' => function ($query) {
                    $query->select('first_name', 'last_name', 'id')
                    ->with(['roles' => function ($query) {
                        $query->select('title');
                    }]);
                },
                'attachment',
            ])->get();
            $members = User::join('comments', function ($join) {
                $join->on('users.id', '=', 'comments.from')->orOn('users.id', '=', 'comments.to');
            })->groupBy('users.id')->where('comments.task_id', $task_id)
                ->select('users.*')->get();
            return view('tasks.chat', compact('task', 'messages', 'members'));
        } catch (Exception $exception) {
            flash()->error($exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|JsonResponse|View
     */
    public function listWithBillableComments(Request $request)
    {
        try {
            $tasks = Task::with(['project.client_company', 'billable_comments'])
                ->whereIn('id', $request->get('tasks'))->get();
            return view('invoices.create', compact('tasks'));
        } catch (Exception $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mark task completed
     *
     * @param int $id
     * @return JsonResponse
     */
    public function markCompleted(int $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update(['completed_at' => date('Y-m-d')]);
            // Update project last updated at
            $task->project()->update([
                'last_updated_at' => Carbon::now(),
            ]);
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                $name = auth()->user()->name;
                // Send notification to user
                sendNotification($user->id, "{$task->name} has marked completed -- " . $name, route('tasks.show', $task->id));
            }
            return response()->json([
                'status_code' => JsonResponse::HTTP_OK,
                'message' => 'Task marked as completed successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Task does not exists!',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // archive task
    public function archive(int $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update([
                'last_updated_at' => Carbon::now(),
            ]);
            $task->delete();
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                // Send notification to user
                sendNotification($user->id, "{$task->name} has been archived", route('tasks.archived'));
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Task does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Task archived successfully',
        ], JsonResponse::HTTP_OK);
    }

    // get all archived tasks
    public function archived()
    {
        return view('tasks.archived');
    }

    public function archivedTasks()
    {
        $tasks = Task::onlyTrashed()->with(['user', 'project'])
            ->whereHas('project', function ($query) {
                if ($this->auth_user->hasRole('client')) {
                    $query->whereClientCompanyId($this->auth_user->company_id);
                } else if ($this->auth_user->hasRole('team-lead')) {
                    $query->whereIn('id', $this->auth_user->projects()->pluck('projects.id'));
                }
            })->withCount(['attachments', 'billable_comments', 'comments_with_time']);
        if ($this->auth_user->hasRole('developer')) {
            $tasks->where('user_id', $this->auth_user->id);
        }
        $tasks = $tasks->get();
        return DataTables::of($tasks)
            ->addColumn('project_name', function ($task) {
                return optional($task->project)->name;
            })->editColumn('priority', function ($task) {
            return priorityToNum($task->priority);
        })->editColumn('name', function ($task) {
            $attachment = '';
            if ($task->attachments_count > 0) {
                $attachment = '<i class="la la-paperclip"></i>&nbsp;';
            }
            return $attachment . '<a href="' . route('tasks.show', $task->id) . '">' . $task->name . '</a>';
        })->addColumn('time_spend', function ($task) {
            return formatTime($task->comments()->sum('time'));
        })->editColumn('end_date', function ($task) {
            return formatDate($task->end_date);
        })->editColumn('user', function ($task) {
            return ($task->user) ? '<span class="avatar pointer" data-toggle="tooltip" title="' . $task->user->name . '">' . nameToImage($task->user) . '</span>' : '';
        })->addColumn('status', function ($task) {
            $html = '';
            if (!empty($task->completed_at)) {
                $html = '<i class="la la-check-circle text-green"></i>';
            }
            return $html;
        })->addColumn('actions', function ($task) {
            if ($this->auth_user->hasAnyPermission(['edit_tasks', 'view_tasks', 'delete_tasks', 'add_comments', 'mark_completed'])) {
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
                if ($this->auth_user->hasPermissionTo('delete_tasks')) {
                $keys = json_encode([ 'total' => '#totalTasks', 'active' => '#activeTasks', 'archived' => '#archivedTasks']);
                $actions .= "<li class='m-nav__item'><a data-table='dt-bs4-tasks' class='m-nav__link btn-restore' data-counter-path='" . route("tasks.counters") . "' data-keys='" . $keys . "' href='" . route("tasks.restore", $task->id) . "'>
                        <span class='m-nav__link-text'>Restore</span>
                    </a></li><li class='m-nav__item'><a data-table='dt-bs4-tasks' class='m-nav__link btn-delete' data-counter-path='" . route("tasks.counters") . "' data-keys='" . $keys . "' href='" . route("tasks.destroy", $task->id) . "'>
                        <span class='m-nav__link-text'>Delete Task</span>
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
        })->rawColumns(['select', 'priority', 'name', 'user', 'status', 'actions'])->addIndexColumn()->make(true);
    }

    // counter
    public function counters()
    {
        $total_tasks = Task::withTrashed()->count();
        $total_active = Task::count();
        $total_archived = Task::onlyTrashed()->count();

        return response()->json([
            'total' => $total_tasks,
            'active' => $total_active,
            'archived' => $total_archived,
        ], 200);
    }

    public function restore(int $id)
    {
        try {
            $task = Task::onlyTrashed()->findOrFail($id);
            $task->restore();
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                // Send notification to user
                sendNotification($user->id, "{$task->name} has been restored", route('tasks.show', $task->id));
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Task does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Task restored successfully',
        ], JsonResponse::HTTP_OK);
    }

    public function store_attachments(Request $request)
    {
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
                    'attachment_id' => $request->task_id,
                    'attachment_type' => 'App\Task',
                ]);
            }
        }
    }

    public function showAttachments(Request $request)
    {
        $attachments = Task::find($request->task_id)->attachments;

        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'attachments' => $attachments,
        ], JsonResponse::HTTP_OK);
    }

    public function deleteAttachments(Request $request)
    {
        try {
            DB::beginTransaction();
            $attachment = Attachment::where('id', $request->id)->firstOrFail();

            if ($attachment->file)
                deleteFile($attachment->file);

            $attachment->delete();

            DB::commit();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'attachment' => 'Attachment deleted successfully.',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
