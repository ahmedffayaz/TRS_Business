<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\Jobs\LeaveRequestNotification;
use App\Models\Leave;
use Carbon\Carbon;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LeavesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (!$this->auth_user->hasPermissionTo('view_leaves')) {
            abort(401);
        }
        $users = User::where('account_type', 'active')->whereHas('roles', function ($query) {
            $query->where('name', '!=', 'client');
        })->get()->pluck('name', 'id');
        if ($request->ajax()) {
            $date = Carbon::now()->format('Y-m');
            if ($this->auth_user->hasPermissionTo('approve_leaves')) {
                $leaves = Leave::with(['user', 'processor'])->whereRaw('DATE_FORMAT(start_date, "%Y-%m")', $date)
                    ->whereRaw('DATE_FORMAT(end_date, "%Y-%m")', $date)->get();
            } else {
                $leaves = Leave::with('processor')
                    ->where('user_id', $this->auth_user->id)
                    ->whereRaw('DATE_FORMAT(start_date, "%Y-%m")', $date)
                    ->whereRaw('DATE_FORMAT(end_date, "%Y-%m")', $date)->get();
            }
            return DataTables::of($leaves)->editColumn('user', function ($leave) {
                if ($leave) {
                    return '<a href="' . route('users.show', $leave->user->id) . '">' . $leave->user->first_name . ' ' . $leave->user->last_name . '</a>';
                }
                return '';
            })->addColumn('no_of_leaves', function ($leave) {
                if ($leave) {
                    return Carbon::parse($leave->end_date)->diffInDays(Carbon::parse($leave->start_date)) + 1;
                }
                return '';
            })->editColumn('status', function ($leave) {
                $badge = $leave->status === 'pending' ? 'warning' : ($leave->status === 'approved' ? 'success' : 'danger');
                return "<label class='badge badge-{$badge}'>{$leave->status}</label>";
            })->addColumn('actions', function ($leave) {
                $actions = '';
                if ($this->auth_user->hasPermissionTo('approve_leaves')) {
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
                                <ul class="m-nav">
                                 <li class="m-nav__item">
                                  <a class="m-nav__link approve-leaves" href="javascript:void(0)"
                                    data-id="' . $leave->id . '">
								    <span class="m-nav__link-text">Approve Leave(s)</span>
							      </a>
							     </li>
							     <li class="m-nav__item">
                                  <a class="m-nav__link reject-leaves" href="javascript:void(0)"
                                    data-id="' . $leave->id . '">
								    <span class="m-nav__link-text">Reject Leave(s) Request</span>
							      </a>
							     </li>
							     <li class="m-nav__item">
                                  <a class="m-nav__link cancel-leaves-request" href="javascript:void(0)"
                                    data-route="' . route('leaves.destroy', $leave->id) . '">
								    <span class="m-nav__link-text">Cancel Leave(s) Request</span>
							      </a>
							     </li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>';
                } else {
                    if ($leave->status === 'pending') {
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
                                <ul class="m-nav">
                                 <li class="m-nav__item">
                                  <a class="m-nav__link cancel-leaves-request" href="javascript:void(0)"
                                    data-route="' . route('leaves.destroy', $leave->id) . '">
								    <span class="m-nav__link-text">Cancel Leave(s) Request</span>
							      </a>
							     </li>
							    </ul>
							  </div>
							</div>
						  </div>
						</div>
					  </div>';
                    }
                }
                return $actions;
            })->rawColumns(['user', 'status', 'actions'])->addIndexColumn()->make(true);
        } else {
            return view('leaves.index', compact('users'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:' . Carbon::now()->format('Y-m-d'),
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'sometimes',
            'is_working'=>'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
            // $leave = Leave::where('user_id', $this->auth_user->id)
            //     ->where(function ($query) use ($request) {
            //         $query->where(function ($query) use ($request) {
            //             $query->whereBetween('start_date', [$request->get('start_date'), $request->get('end_date')]);
            //         })->orWhere(function ($query) use ($request) {
            //             $query->whereBetween('end_date', [$request->get('start_date'), $request->get('end_date')]);
            //         });
            //     })
            //     ->first(); 
           $action = 'created';
            $inputs = $request->all();  
            $inputs['user_id'] = $request->has('user_id') ? $request->get('user_id') : $this->auth_user->id;
            $leave = Leave::where('user_id', $inputs['id'])
                ->where('id', '!=', $inputs['id'])
                ->where(function ($query) use ($inputs) {
                    $query->whereBetween('start_date', [$inputs['start_date'], $inputs['end_date']])
                        ->orWhereBetween('end_date', [$inputs['start_date'], $inputs['end_date']]);
                     
                })->first();  
                
        if ($leave) {
            return response()->json([
                'message' => 'You have already requested some of the specified days',
            ], 409);
        }
        if (!empty($inputs['id'])) {
            $attendance = Leave::findOrFail($inputs['id']);
            $attendance->update($inputs);
            $action = 'updated';            
            $leave = Leave::findOrFail($inputs['id']);
        }
       else{
        if (auth()->user()->roles[0]->name == 'admin') {
            $status = 'approved';
        } else {
            $status = 'pending';
        }
            $leave = Leave::create([
                'user_id' => $request->get('user_id'),
                'status' => $status,
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'is_working' => $request->get('is_working'),
                'reason' => $request->get('reason'),
            
            ]);    
        }
        $days = dateDiff($leave->end_date, $leave->start_date);
        $end_date = $leave->end_date;
        if ($days > 0) {
            $end_date = date('Y-m-d', strtotime($leave->end_date . ' + 1 day'));
        }
       
        $event = [
            'title' => ($leave->is_working == '1' ? 'Work from home' : ($leave->is_working == '2' ? 'Half Leave' : 'Leave '))
            . '<br>' . $leave->user()->first()->name .'<br>Status : '.$leave->status. '<br/>Days: ' . (($days === 0) ? 1 : $days ),
            'start' => $leave->start_date,
            'end' => $end_date .'<br>',
            'editable' => true,
            'user_id' => $leave['user_id'],
            'auth_user_id' => $this->auth_user->id,
            'color' => $leave->is_working == '1' ? '#38c172' : '#e3342f',
            'evt' => $leave,           
        ];

        // Send email to admin(s)
         $this->dispatch(new LeaveRequestNotification($leave));
        
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
             'message' => $action === 'created'
             ? 'Leave marked successfully!' : 'Leave updated successfully',
            // 'message' => 'Leave(s) request submitted successfully',
            'event' => $event,
           
        ]);
    }

    public function process(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $leave = Leave::findOrFail($request->get('id'));
            DB::beginTransaction();
            $leave->update([
                'status' => $request->get('status'),
                'processing_reason' => $request->get('processing_reason'),
                'processed_by' => $this->auth_user->id,
            ]);
            // Add entries to attendance table
            if ($request->get('status') === 'approved') {
                Attendance::create([
                    'user_id' => $leave->user_id,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'is_working' => 0,
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Leave(s) processed successfully',
            ]);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $leaves = Leave::findOrFail($id); 
                     
           if((auth()->user()->id == $leaves->user_id) || ($this->auth_user->hasPermissionTo('delete_leaves'))){
                $leaves->delete();
            }
           
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Leave(s) request cancelled successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}