<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Models\Comment;
use App\Events\Message;
use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CommentsController extends Controller
{
    public function index(int $id)
    {
        $task = Task::find($id);
        $query = $task->comments()->with(['to_user', 'from_user']);

        if (request()->has('type') && !empty(request()->get('type'))) {
            $query->where('type', request()->get('type'));
        }

        $comments = $query->get();

        return DataTables::of($comments)
            ->editColumn('time', function ($comment) {
                return formatTime($comment->time);
            })->editColumn('not_billable_time', function ($comment) {
            return formatTime($comment->not_billable_time);
        })->editColumn('from', function ($comment) {
            if ($comment->from_user) {
                return '<a href="' . route('users.show', $comment->from_user->id) . '">' . $comment->from_user->name . '</a>';
            }
            return '';
        })->editColumn('dated', function ($comment) {
            return formatDate($comment->dated);
        })->editColumn('invoiced_at', function ($comment) {
            return formatDate($comment->invoiced_at);
        })->editColumn('created_at', function ($comment) {
            return formatDate($comment->created_at);
        })->addColumn('actions', function ($comment) use ($task) {
            if ($this->auth_user->hasAnyPermission(['edit_comments', 'delete_comments']) && empty($task->billed_at) && empty($comment->invoiced_at)) {
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
                if ($this->auth_user->hasPermissionTo('edit_comments')) {
                    $actions .= '<li class="m-nav__item"><a data-id="' . $comment->id . '" class="m-nav__link btn-edit-comment"
								data-url="' . route('comments.edit', $comment->id) . '" href="javascript:void(0)">
						   <span class="m-nav__link-text">Edit comment</span>
					    </a></li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_comments') && isOwner($comment->from)) {
                    $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-comments" class="m-nav__link btn-sm btn-delete" href="' . route('comments.destroy', $comment->id) . '">
								<span class="m-nav__link-text">Delete comment</span>
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
        })->rawColumns(['dated', 'from', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Edit comment modal data query
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            // Add unit
            $unit = "mins";
            if ($comment->time > 60) {
                if ($comment->time % 60 === 0) {
                    $unit = "hrs";
                    $comment->time = $comment->time / 60;
                }
            }
            $comment->unit = $unit;
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $comment,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Comment does not exist!',
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Comment $comment)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'message' => 'required',
            'time' => 'nullable|integer|min:10',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $type = 'comment';
        $message = $request->get('message');
        if ($request->has('file')) {
            $type = 'attachment';
            $message = 'file';
        }
        if ($request->has('time') && !empty($request->get('time'))) {
            $type = 'time';
        }
        $is_billable = isset($request->is_billable) ? 1 : 0;
        $is_update = false;
        if ($request->has('comment_id') && !empty($request->get('comment_id'))) {
            $is_update = true;
            $comment = Comment::findOrFail($request->get('comment_id'));
        }

        $inputs = [
            'description' => $message,
            'time' => $request->has('time') ? $request->get('time') : '',
            'type' => $type,
            'from' => $is_update ? $comment->from : auth()->user()->id,
            'is_billable' => $is_billable,
            'task_id' => $request->get('task_id'),
            'dated' => $request->get('dated'),
        ];

        if ($is_update) {
            $comment->update($inputs);
            $msg = 'Comment updated successfully!';
        } else {
            $comment = $comment->create($inputs);
            $msg = 'Message sent successfully!';
        }
        $comment->task->project()->update([
            'last_updated_at' => Carbon::now(),
        ]);
        if ($request->has('file')) {
            // Upload file
            $mimes = $request->file('file')->getMimeType();
            if ($mimes == 'video/quicktime') {
                $mimes = 'video/mp4';
            }
            $file_name = md5(time()) . '.' . $request->file('file')->getClientOriginalExtension();
            $file = Storage::disk('public')->putFileAs('/files', $request->file('file'), $file_name);
            Attachment::create([
                'name' => 'file',
                'mimes' => $mimes,
                'file' => $file,
                'attachment_id' => $comment->id,
                'attachment_type' => 'App\Comment',
            ]);
        }

        $comment = Comment::with([
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
        ])->find($comment->id);
        event(new Message($comment->toArray()));

        // counting number of hours
        $time = Comment::where('from', auth()->user()->id)
            ->whereDate('dated', $request->get('dated'))->sum('time');
        if (formatTime($time) > 8) {
            $msg .= ' You have added more than 8 working hours';
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => $msg,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response|JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if (empty($comment->billed_at)) {
                $comment->task->project()->update([
                    'last_updated_at' => Carbon::now(),
                ]);
                $comment->delete();
            } else {
                return response()->json([
                    'status' => JsonResponse::HTTP_UNAUTHORIZED,
                    'message' => 'You are not allowed to delete billed entry!',
                ], JsonResponse::HTTP_UNAUTHORIZED);
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Comment does not exists!',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Comment deleted successfully',
        ], JsonResponse::HTTP_OK);
    }
}
