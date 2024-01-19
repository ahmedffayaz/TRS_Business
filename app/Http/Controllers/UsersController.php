<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Company;
use App\Contract;
use App\Http\Requests\UserRequest;
use App\Salary;
use App\Models\Task;
use App\Models\User;
use App\UserContract;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:add_users'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:edit_users'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:view_users'], ['only' => ['index', 'show', 'users']]);
        $this->middleware(['permission:delete_users'], ['only' => ['destroy']]);
        $this->middleware(['permission:view_user_contracts'], ['only' => ['getContracts']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Return users as json
     *
     * @return mixed
     * @throws Exception
     */
    public function users()
    {
        $classes = ['avatar-info', 'avatar-danger', 'avatar-success'];
        $users = User::withTrashed()->with(['roles'])->where('account_type','active')->orderBy('account_type');
        return DataTables::of($users)
            ->addColumn('user', function ($user) use ($classes) {
                $image = getAvatar($user);
                $url = route('users.show', $user->id);
                $c = $classes[array_rand($classes)];
                return "<div class='d-flex align-items-center'>
                    <div class='mr-2 avatar-square {$c}'>
                        {$image}
                    </div>
                    <div class='font-weight-medium'>
                        <a class='text-hover-primary' href='{$url}'>{$user->first_name} {$user->last_name}</a>
                        <p class='mb-0'>Email: <a class='text-hover-light-gray' href='mailto:{$user->email}'>{$user->email}</a></p>
                    </div>
                </div>";
            })->addColumn('status', function ($user) {
                return formatUserStatus($user->account_type);
            })->editColumn('roles', function ($user) {
                $colors = ['info', 'primary', 'success', 'danger', 'warning'];
                shuffle($colors);
                $rand = rand(0, 4);
                $color = $colors[$rand] . ' m-badge--wide mr-2';
                return wrapWithLabel($user->roles, 'title', null, $color);
            })->addColumn('actions', function ($user) {
                if ($this->auth_user->hasAnyPermission(['edit_users', 'view_users', 'delete_users'])) {
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
                    if ($this->auth_user->hasPermissionTo('edit_users')) {
                        $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('users.edit', $user->id) . '">
								<span class="m-nav__link-text">Edit user</span>
							</a></li>';
                    }
                    if ($this->auth_user->hasPermissionTo('view_contracts')) {
                        $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('user-contracts', $user->id) . '">
								<span class="m-nav__link-text">User Terms & Conditions</span>
							</a></li>';
                    }
                    if ($this->auth_user->hasPermissionTo('view_users')) {
                        $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('users.show', $user->id) . '">
								<span class="m-nav__link-text">View user details</span>
							</a></li>';
                    }
                    if ($this->auth_user->hasPermissionTo('delete_users')) {
                        $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-users" class="m-nav__link btn-delete" href="' . route('users.destroy', $user->id) . '">
								<span class="m-nav__link-text">Delete users</span>
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
            })->rawColumns(['roles', 'user', 'status', 'actions'])->addIndexColumn()->make(true);
    }

    public function restore(int $id)
    {
        try {
            $user_record = User::withTrashed()->findOrFail($id);
            $user_record->account_type ='active';
            $user_record->restore();
            $users = User::role('admin')->get();
            foreach ($users as $user) {
                // Send notification to user
                sendNotification($user->id, "{$user_record->name} has been restored", route('users.show', $user_record->id));
            }
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'User restored successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Users does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    public function notifications($user_id)
    {
        $notifications = User::find($user_id)->notifications()->new()->get();
        return response()->json([
            'data' => $notifications,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('title', 'id');
        $companies = ['' => 'Select Company'] + Company::pluck('name', 'id')->all();
        return view('users.create', compact('roles', 'companies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     *
     * @param User $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws Exception
     */
    public function store(UserRequest $request, User $user)
    {
        try {
            $inputs = $request->all();
            if (empty($inputs['company_id'])) {
                $inputs['company_id'] = $this->company->id;
            }
            DB::beginTransaction();
            $user = $user->create($inputs);
            // Sync roles
            $user->roles()->sync($inputs['roles']);
            DB::commit();
            flash('User added successfully', 'success');
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            flash($exception->getMessage(), 'error');
        } catch (Exception $exception) {
            DB::rollBack();
            flash($exception->getMessage(), 'error');
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $user = User::with(['roles'])->withCount('commentsTo')->findOrFail($id);

        } catch (ModelNotFoundException $exception) {
            flash($exception->getMessage(), 'error');
            return redirect()->back();
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        try {
            $user = User::with(['roles:name,id', 'company:name,id'])->findOrFail($id);
            $roles = Role::pluck('title', 'id');
            $companies = ['' => 'Select Company'] + Company::pluck('name', 'id')->all();
            return view('users.edit', compact('roles', 'user', 'companies'));
        } catch (ModelNotFoundException $exception) {
            flash($exception->getMessage(), 'error');
            return redirect()->route('users.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     * @throws Exception
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $inputs = $request->all();
            $user = User::with(['roles'])->findOrFail($id);
            DB::beginTransaction();
            if (empty($inputs['password'])) {
                unset($inputs['password']);
            }
            $user->update($inputs);
            // Sync roles
            $user->roles()->sync($inputs['roles']);
            // Update projects salaries for cost calculation
            if ($user->account_type === 'active') {
                $projects = $user->projects()->where('status', '!=', 'delivered')->get();
                $salary_per_hour = $user->salary / (22 * 8);
                foreach ($projects as $project) {
                    $project_salary = $project->latestSalary();
                    if (!$project_salary || $project_salary->salary !== $user->salary) {
                        if ($project_salary) {
                            // Close the old period of salary
                            $project_salary->update([
                                'end_date' => Carbon::now()->subtract('day', 1)->format('Y-m-d'),
                            ]);
                        }
                        $project->salaries()->save(new Salary([
                            'user_id' => $user->id,
                            'start_date' => Carbon::now()->format('Y-m-d'),
                            'salary' => $user->salary,
                            'salary_per_hour' => $salary_per_hour,
                        ]));
                    }
                }
            } else if($user->account_type === 'de-active'){
                $user->delete();

            }
            DB::commit();
            flash('User information updated successfully', 'success');
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            flash($exception->getMessage(), 'error');
        } catch (Exception $exception) {
            DB::rollBack();
            flash($exception->getMessage(), 'error');
        }
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function archivedUsers()
    {
        $classes = ['avatar-info', 'avatar-danger', 'avatar-success'];
        $users = User::withTrashed()->with(['roles'])->where('account_type', 'de-active')->orderBy('account_type');
       // $users = User::with(['roles'])->where('account_type', 'de-active')->orderBy('account_type');
        return DataTables::of($users)
            ->addColumn('user', function ($user) use ($classes) {
                $image = getAvatar($user);
                $url = route('users.show', $user->id);
                $c = $classes[array_rand($classes)];
                return "<div class='d-flex align-items-center'>
                    <div class='mr-2 avatar-square {$c}'>
                        {$image}
                    </div>
                    <div class='font-weight-medium'>
                        <a class='text-hover-primary' href='{$url}'>{$user->first_name} {$user->last_name}</a>
                        <p class='mb-0'>Email: <a class='text-hover-light-gray' href='mailto:{$user->email}'>{$user->email}</a></p>
                    </div>
                </div>";
            })->addColumn('status', function ($user) {
                return formatUserStatus($user->account_type);
            })->editColumn('roles', function ($user) {
                $colors = ['info', 'primary', 'success', 'danger', 'warning'];
                shuffle($colors);
                $rand = rand(0, 4);
                $color = $colors[$rand] . ' m-badge--wide mr-2';
                return wrapWithLabel($user->roles, 'title', null, $color);
            })->addColumn('actions', function ($user) {
            if ($this->auth_user->hasAnyPermission(['edit_users', 'view_users', 'delete_users'])) {
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
                if ($this->auth_user->hasPermissionTo('delete_users')) {
                $keys = json_encode([ 'total' => '#totalUsers', 'active' => '#activeUsers', 'archived' => '#archivedUsers']);
                $actions .= "<li class='m-nav__item'><a data-table='dt-bs4-users' class='m-nav__link btn-restore' data-counter-path='" . route("users.counters") . "' data-keys='" . $keys . "' href='" . route("users.restore", $user->id) . "'>
                        <span class='m-nav__link-text'>Restore</span>
                    </a></li><li class='m-nav__item'><a data-table='dt-bs4-users' class='m-nav__link btn-delete' data-counter-path='" . route("users.counters") . "' data-keys='" . $keys . "' href='" . route("users.destroy", $user->id) . "'>
                        <span class='m-nav__link-text'>Delete User</span>
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
        })->rawColumns(['roles', 'user', 'status', 'actions'])->addIndexColumn()->make(true);
    }
    public function counters()
    {
       $total_users = User::withTrashed()->count();
       $total_active = User::where('account_type','active')->orderBy('account_type')->count();
       $total_archived = User::withTrashed()->where('account_type','de-active')->orderBy('account_type')->count();

        return response()->json([
           'total' => $total_users,
            'active' => $total_active,
           'archived' => $total_archived,
        ], 200);
    }
     // archive users
     public function archive(int $id)
     {
         try {
             $user = User::findOrFail($id);
             $user->update([
                 'last_updated_at' => Carbon::now(),
             ]);
             $user->delete();
             $users = User::role('admin')->get();
             foreach ($users as $user) {
                 // Send notification to user
                 sendNotification($user->id, "{$user->name} has been archived", route('users.archived'));
             }
             return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'User archived successfully',
            ], JsonResponse::HTTP_OK);
         } catch (ModelNotFoundException $exception) {
             return response()->json([
                 'status' => JsonResponse::HTTP_NOT_FOUND,
                 'message' => 'User does not exists!',
             ], JsonResponse::HTTP_NOT_FOUND);
         } catch (Exception $exception) {
             return response()->json([
                 'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                 'message' => $exception->getMessage(),
             ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
         }

     }
      // get all archived users
    public function archived()
    {
        return view('users.archived');
    }
    public function destroy($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if ($user->forceDelete()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'message' => 'User deleted successfully',
                ], JsonResponse::HTTP_OK);
            }
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal Server Error',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (QueryException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'This user has data, please delete that first',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function showUpdatePasswordView()
    {
        return view('users.update-password');
    }

    public function updatePassword(Request $request)
    {
        $validatedData = $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            if (Hash::check($validatedData['old_password'], $this->auth_user->getAuthPassword())) {
                $this->auth_user->update([
                    'password' => $validatedData['password'],
                ]);
                flash()->success('Password updated successfully');
            } else {
                flash()->error('Old password is invalid');
            }
        } catch (ModelNotFoundException $exception) {
            flash()->error('Old password is invalid');
        }
        return redirect()->back();
    }

    public function showProfileView()
    {
        return view('users.profile');
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->auth_user->id . ',id',
            'phone' => 'required',
            'address' => 'required',
            'alternative_number' => 'nullable',
        ]);
        $this->auth_user->update($validatedData);
        flash()->success('Profile updated successfully');
        return redirect()->back();
    }

    /**
     * Mark read notification
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function markRead()
    {
        try {
            $user = User::findOrFail(request()->get('userId'));
            foreach ($user->unreadNotifications as $notification) {
                if ($notification->id == request()->get('notifiableId')) {
                    $notification->markAsRead();

                    return response()->json(['message' => 'Marked read successfully'], 200);
                }
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Please provide correct data'], 400);
        } catch (Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 200);
        }

        return false;
    }

    public function chartData($user_id)
    {
        // Chart data
        $start_date = Carbon::now()->submonths(3);
        $end_date = Carbon::now();
        $data = Comment::selectRaw('SUM(time) as y, DATE(dated) as x')
            ->whereIn('user_id', function ($query) use ($user_id) {
                $query->select('id')->from(with(new User)->getTable())
                    ->where('user_id', $user_id);
            })->where('type', 'time')->whereBetween('dated', [$start_date, $end_date])
            ->groupBy(DB::raw('x'))->orderBy('x', 'ASC')->get();
        $dates = [];
        $formattedData = [];
        if (count($data) > 0) {
            $period = new DatePeriod(new DateTime($data[0]->x), new DateInterval('P1D'), new DateTime($data[count($data) - 1]->x . ' +1 day'));
            foreach ($period as $key => $date) {
                $formattedDate = $date->format("Y-m-d");
                $dates[] = formatDate($formattedDate);
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

    // upload profile picture
    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();
        $file_name = md5(time()) . '.' . $request->file('avatar')->getClientOriginalExtension();
        $filePath = Storage::disk('public')->putFileAs('/profile', $request->file('avatar'), $file_name);
        $user->update([
            'avatar' => $filePath,
        ]);
        flash()->success('Avatar uploaded successfully');
        return redirect()->back();
    }
    // save contract
    public function saveContract(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'digital_signature' => 'required_if:digital_signature_pad,'.null,
            'check' => 'required',
        ], [
            'digital_signature.required_if' => 'Signature is required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
            $contract = Contract::findOrFail($request->contract_id);
            $path = isset($request->digital_signature_pad) ? $request->digital_signature_pad : saveResizeImage($request->digital_signature, 'signatures', 1024);

            $pdf = App::make('dompdf.wrapper');
            $pdf->setOptions(['isPhpEnabled' => true])->setPaper('a4', 'portrait');
            $file_name = md5(time() . auth()->user()->name) . ' contract' . '.pdf';
            if (!Storage::disk('public')->exists(getStoragePath('user-contracts'))) {
                Storage::disk('public')->makeDirectory(getStoragePath('user-contracts'));
            }

            $contract_path = public_path('storage/' . getStoragePath('user-contracts')) . '/' . $file_name;
            $view = 'users.contract_template';

            $user_contract = UserContract::create([
                'user_id' => auth()->user()->id,
                'contract_id' => $request->contract_id,
                'uuid' => md5(time()) . Str::random(),
                'signature_url' => $path,
                'pdf_url' => 'storage/' . getStoragePath('user-contracts') . '/' . $file_name,
            ]);

            $pdf->loadView($view, compact('contract', 'user_contract'))->save($contract_path);

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Contract saved successfully',
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getContracts($user_id)
    {
        if (!$this->auth_user->hasRole('admin') && $user_id != $this->auth_user->id)
            abort(403);

        return view('users.user_contracts', compact('user_id'));
    }

    public function userContractsDatatable($user_id)
    {
        $user_contracts = UserContract::with('contract')->where('user_id', $user_id)->get();
        return DataTables::of($user_contracts)
            ->addColumn('version', function ($record) {
                return $record->contract->version;
            })
            ->addColumn('title', function ($record) {
                return $record->contract->title;
            })
            ->addColumn('description', function ($record) {
                return addEllipsis($record->contract->description);
            })
            ->addColumn('created_at', function ($record) {
                return formatDate($record->created_at);
            })
            ->addColumn('actions', function ($contract) {
                $btn = '<a data-id="' . $contract->id . '" class="m-nav__link btn-edit-contact  btn btn-info btn-sm" href="' . asset($contract->pdf_url) . '" target="_blank">
                                  <span class="m-nav__link-text">View</span>
                                </a>';
                $btn .= '<a data-id="' . $contract->id . '" class="ml-2 m-nav__link btn-edit-contact  btn btn-primary btn-sm" href="' . asset($contract->pdf_url) . '" download>
                                    <span class="m-nav__link-text">Download</span>
                                </a>';
                // $btn = $btn . ' <a href="javascript::" data-toggle="tooltip" data-method="get" data-table="categories_table" data-url="' . route('contracts.destroy', $contract->id) . '" class="btn btn-danger btn-sm delete">Delete</a>';
                return $btn;
            })
            ->rawColumns(['version', 'description', 'created_at', 'actions'])->addIndexColumn()->make(true);
    }

    public function uploadDigitalSignature(Request $request)
    {
        try {
            DB::beginTransaction();
            $dir = getStoragePath('users');
            // Upload file if exists
            if ($request->file('digital_signature')) {
                $now = Carbon::now()->timestamp;
                $digital_signature = 'signature_' . $now . '.png';
                $request->file('digital_signature')->storeAs($dir, $digital_signature, 'public');
                $inputs['digital_signature'] = $digital_signature;
            }
            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Contract saved successfully',
                'data' => ['signature' => 'images/users/' . $digital_signature],
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
