<?php

namespace App\Http\Controllers;

use App\Contract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ContractController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:view_contracts'], ['only' => ['index', 'contracts']]);
        $this->middleware(['permission:add_contracts|edit_contracts'], ['only' => ['store']]);
        $this->middleware(['permission:view_contracts'], ['only' => ['show']]);
        $this->middleware(['permission:edit_contracts'], ['only' => ['edit']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::pluck('title', 'id')->all();
        return view('contracts.index', compact('roles'));
    }

    public function contracts()
    {
        $contracts = Contract::orderBy('id', 'desc');
        return DataTables::of($contracts)
            ->editColumn('description', function ($contract) {
                return addEllipsis($contract->description);
            })->editColumn('users_count', function ($contract) {
            return $contract->version;
        })->addColumn('actions', function ($contract) {
            $btn = "";
            if($this->auth_user->hasPermissionTo('edit_contracts')) {
                $btn = '<a data-id="' . $contract->id . '" class="m-nav__link btn-edit-contact btn btn-primary btn-sm"
                                        data-url="' . route('contracts.edit', [$contract->id]) . '" href="javascript:void(0)">
                                        <span class="m-nav__link-text">Edit</span>
                                    </a>';
            }
            // $btn = $btn . ' <a href="javascript::" data-toggle="tooltip" data-method="get" data-table="categories_table" data-url="' . route('contracts.destroy', $contract->id) . '" class="btn btn-danger btn-sm delete">Delete</a>';
            return $btn;
        })
            ->rawColumns(['name', 'description', 'version', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'version' => 'required_if:contract_id,null' . $request->contract_id . ',id',
            'title' => 'required',
            'roles' => 'required',
        ]
        , [
            'version.unique' => 'Your version number is must be a unique',
            'version.null' => 'Your version number field is required',
        ]
    );
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
            if ($request->contract_id) {
                $contract = Contract::with('roles')->findOrFail($request->contract_id);
                $version = $this->getVersion($request->contract_id);
                    $contract->update([
                        'title' => $request->title,
                        'description' => $request->description,
                        'version' => $version,
                    ]);
                    $contract->roles()->sync($request->roles);
            }
            else {
                $contract = Contract::create([
                    'uuid' => getUuid(),
                    'title' => $request->title,
                    'description' => $request->description,
                    'version'=> '1',
                ]);
                $contract->roles()->sync($request->roles);
           }

            DB::commit();
            if (Auth::user()->roles()->first()->id == $request->role_id) {
                return response()->json([
                    'success' => JsonResponse::HTTP_OK,
                    'message' => 'Contract updated successfully',
                    'reload' => true,
                ], JsonResponse::HTTP_OK);
            }
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Contract added successfully',
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $contract = Contract::with('roles:name,id')->findOrFail($id);
            $contract_val = ($contract->version  ? $contract->version : 1);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => $contract,
                'contract_val' => $contract_val,
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getVersion($contract_id)
    {
       $contract = Contract::findOrFail($contract_id);
        return $contract->version += 0.1;
    }
}
