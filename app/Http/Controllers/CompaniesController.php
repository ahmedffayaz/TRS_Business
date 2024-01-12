<?php

namespace App\Http\Controllers;

use App\Company;
use App\Country;
use App\Http\Requests\CompanyRequest;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class CompaniesController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:add_companies'], ['only' => ['create','store']]);
        $this->middleware(['permission:edit_companies'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:view_companies'], ['only' => ['index', 'companies']]);
        $this->middleware(['permission:delete_companies'], ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('companies.index');
    }

    /**
     * Return companies as json
     *
     * @return mixed
     * @throws Exception
     */
    public function companies()
    {
        $companies = Company::withCount(['users'])->orderBy('id', 'desc');
        return DataTables::of($companies)->editColumn('name', function ($company) {
            if ($company) {
                return '<a href="' . route('companies.show', $company->id) . '">' . $company->name . '</a>';
            }
            return '';
        })->editColumn('users_count', function ($company) {
            return wrapWithLabel($company->users_count);
        })->addColumn('actions', function ($company) {
            if ($this->auth_user->hasAnyPermission(['edit_companies', 'view_companies', 'delete_companies'])) {
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

                if ($this->auth_user->hasPermissionTo('edit_companies')) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('companies.edit', $company->id) . '">
								<span class="m-nav__link-text">Edit company</span>
							</a></li>';
                }
                if ($this->auth_user->hasPermissionTo('view_companies')) {
                    $actions .= '<li class="m-nav__item"><a class="m-nav__link" href="' . route('companies.show', $company->id) . '">
								<span class="m-nav__link-text">View company details</span>
							</a></li>';
                }
                if ($this->auth_user->hasPermissionTo('delete_companies')) {
                    $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-companies" class="m-nav__link btn-delete" href="' . route('companies.destroy', $company->id) . '">
								<span class="m-nav__link-text">Delete company</span>
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
          <i class="la la-lock"></i>
        </button>';
            }
            return $actions;
        })->rawColumns(['name', 'users_count', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::pluck('title', 'id');
        $countries = ['' => 'Select Country'] + Country::pluck('name', 'name')->all();
        $busniesses = Company::whereType('business')->pluck('name', 'id')->all();
        return view('companies.create', compact('countries', 'busniesses', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CompanyRequest|Request $request
     * @param Company $company
     *
     * @return Response
     */
    public function store(CompanyRequest $request, Company $company)
    {
        DB::beginTransaction();
        $inputs = $request->all();
        if ($inputs['type'] == 'client') {
            $inputs['parent_id'] = $request->parent_id;
        }
        if ($request->has('logo') && !empty($request->file('logo'))) {
            $file_name = md5(time()) . '.' . $request->file('logo')->getClientOriginalExtension();
            $inputs['logo'] = Storage::disk('public')->putFileAs('/images', $request->file('logo'), $file_name);
        }
        $company = $company->create($inputs);
        if ($request->type === 'client' && $request->input('add_user') == 'on') {
            foreach ($request->first_name as $index => $first_name) {
                $validator = Validator::make(['email' => $request->email[$index]], [
                    'email' => 'required|email|unique:users',
                ]);
                if ($validator->fails()) {
                    DB::rollBack();
                    flash()->error('Client email already taken');
                    return redirect()->back();
                }
                $user = User::create([
                    'first_name' => $first_name,
                    'last_name' => $request->last_name[$index],
                    'email' => $request->email[$index],
                    'password' => $request->password[$index],
                    'company_id' => $company->id,
                    'account_type' => 'active',
                ]);
                $user->roles()->sync(2);
            }
        }
        DB::commit();
        flash()->success('Company created successfully');
        return redirect()->route('companies.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        try {
            if ($this->auth_user->hasPermissionTo('view_companies')) {
                    $company = Company::with(['company', 'projects' => function ($query) {
                    $query->withCount(['invoices']);
                    $query->with(['tasks', 'members', 'openTasks', 'closedTasks']);
                }])->withCount(['projects'])->findOrFail($id);
                return view('companies.show', compact('company'));
                }else{
                    abort(401);
                }
        } catch (ModelNotFoundException $exception) {
            flash()->error('Company does not exists!');
            return redirect()->back();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit(int $id)
    {
        try {
            $roles = Role::pluck('title', 'id');
            $countries = ['' => 'Select Country'] + Country::pluck('name', 'name')->all();
            $company = Company::findOrFail($id);
            $busniesses = Company::whereType('business')->pluck('name', 'id')->all();
            return view('companies.edit', compact('company', 'countries', 'busniesses', 'roles'));
        } catch (ModelNotFoundException $ex) {
            flash()->error("No company found");
            return redirect()->route('companies.index');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CompanyRequest|Request $request
     * @param int $id
     *
     * @return Response
     */
    public function update(CompanyRequest $request, $id)
    {
        try {
            $inputs = $request->all();
            $company = Company::findOrFail($id);
            if ($request->has('logo') && !empty($request->file('logo'))) {
                $file_name = md5(time()) . '.' . $request->file('logo')->getClientOriginalExtension();
                $inputs['logo'] = Storage::disk('public')->putFileAs('/images', $request->file('logo'), $file_name);
                // Remove old file if any
                if (!empty($company->logo) && Storage::disk('public')->exists($company->logo)) {
                    Storage::disk('public')->delete($company->logo);
                }
            }
            $company->update($inputs);
            flash()->success('Company updated successfully');
        } catch (Exception $exception) {
            flash()->error($exception->getMessage());
        }
        return redirect()->route('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy(int $id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Company does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (QueryException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'This company has data, please delete that first',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Company deleted successfully',
        ], JsonResponse::HTTP_OK);
    }

    public function getCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!empty(request('company_id'))) {
            $company = Company::with(['company'])->findOrFail(request('company_id'));
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'data' => $company,
        ], JsonResponse::HTTP_OK);
    }

}