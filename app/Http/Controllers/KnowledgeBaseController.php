<?php

namespace App\Http\Controllers;

use App\KnowledgeBase;
use DB;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('knowledgebase.index', compact('roles'));
    }

    public function knowledgeBase()
    {
        $data = $this->auth_user->hasrole('admin')
            ? KnowledgeBase::get()
            : KnowledgeBase::where(function($query){
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
            })->orWhere(function($q){
                $q->whereDoesntHave('roles');
            })->get();

        return DataTables::of($data)
            ->editColumn('question', function ($knowledgebase) {
                return '<span class="question" data-question="' . $knowledgebase->question . '" data-answer="' . $knowledgebase->answer . '">'
                    . $knowledgebase->question . '</span>';
            })->editColumn('keywords', function ($knowledgebase) {
                $keywords_array = explode(',', $knowledgebase->keywords);
                $html = '<div class="ellipsis">';
                foreach ($keywords_array as $keyword) {
                    $html .= '<a href="javascript:void(0)" class="keyword" data-word="' . $keyword . '">' . $keyword . '</a>&nbsp;';
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('roles', function ($knowledgebase) {
                $colors = ['info', 'primary', 'success', 'danger', 'warning'];
                shuffle($colors);
                $rand = rand(0, 4);
                $color = $colors[$rand] . ' m-badge--wide mr-2';
                return wrapWithLabel($knowledgebase->roles, 'title', null, $color);
            })
            ->addColumn('actions', function ($knowledgebase) {
                if ($this->auth_user->hasAnyPermission(['edit_users', 'view_users', 'delete_users'])) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
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
                    if ($this->auth_user->hasPermissionTo('edit_knowledgebase')) {
                        $actions .= '<a class="m-nav__link edit-knowledgebase-btn" href="javaScript:void(0)"
                                        data-id="' . $knowledgebase->id . '" data-question="' . $knowledgebase->question . '" data-answer="' . $knowledgebase->answer . '"
                                        data-keywords="' . $knowledgebase->keywords . '" data-roles=' . $knowledgebase->roles->pluck('id')->toJson() . '>
                                                <span class="m-nav__link-text">Edit Question</span>
                                            </a>';
                    }
                    if ($this->auth_user->hasPermissionTo('delete_knowledgebase')) {
                        $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-knowledge-base" class="m-nav__link btn-delete" href="' . route('knowledgebase.destroy', $knowledgebase->id) . '">
                                                <span class="m-nav__link-text">Delete knowledgebase</span>
                                            </a></li>';
                    }
                    $actions .= '</ul>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>';
                } else {
                    $actions = '<button class="btn btn-outline-dark btn-sm cursor"  id="actions" disabled>
                    <i class="la la-lock"></i></button>';
                }
                $actions .= '</div>';
                return $actions;
            })->rawColumns(['keywords', 'question', 'roles', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:knowledgebase,id',
            'question' => 'required',
            'answer' => 'required',
            'keywords' => 'required',
        ]);

        try {
            $inputs = $request->all();
            $message = 'Question updated successfully';

            DB::beginTransaction();
            if ($inputs['id']) {
                $knowledgebase = KnowledgeBase::with(['roles'])->findOrFail($inputs['id']);
                $knowledgebase->update($inputs);
            } else {
                $knowledgebase = KnowledgeBase::create($inputs);
                $message = 'Question added successfully';
            }

            $knowledgebase->roles()->sync($request->select_role);

            DB::commit();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => $message
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

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
     public function AdminKb(){
        if (!$this->auth_user->hasRole('admin'))
            abort(403);

        $roles = \Spatie\Permission\Models\Role::all();
        return view('knowledgebase.admin', compact('roles'));
     }
     public function AdminRecord(){
        if (!$this->auth_user->hasRole('admin'))
            abort(403);

        $data = $this->auth_user->hasrole('admin')
            ? KnowledgeBase::get()
            : KnowledgeBase::where(function($query){
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
            })->orWhere(function($q){
                $q->whereDoesntHave('roles');
            })->get();

            $data = KnowledgeBase::where(function($query){
                $query->whereHas('roles', function ($q) {
                    $q->where('name', $this->auth_user->roles()->name='admin');
                });
            });

        return DataTables::of($data)
            ->editColumn('question', function ($knowledgebase) {
                return '<span class="question" data-question="' . $knowledgebase->question . '" data-answer="' . $knowledgebase->answer . '">'
                    . $knowledgebase->question . '</span>';
            })->editColumn('keywords', function ($knowledgebase) {
                $keywords_array = explode(',', $knowledgebase->keywords);
                $html = '<div class="ellipsis">';
                foreach ($keywords_array as $keyword) {
                    $html .= '<a href="javascript:void(0)" class="keyword" data-word="' . $keyword . '">' . $keyword . '</a>&nbsp;';
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('roles', function ($knowledgebase) {
                $colors = ['info', 'primary', 'success', 'danger', 'warning'];
                shuffle($colors);
                $rand = rand(0, 4);
                $color = $colors[$rand] . ' m-badge--wide mr-2';
                return wrapWithLabel($knowledgebase->roles, 'title', null, $color);
            })
            ->addColumn('actions', function ($knowledgebase) {
                if ($this->auth_user->hasAnyPermission(['edit_users', 'view_users', 'delete_users'])) {
                    $actions = '<div class="btn-group">';
                    $actions .= '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
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
                    if ($this->auth_user->hasPermissionTo('edit_knowledgebase')) {
                        $actions .= '<a class="m-nav__link edit-knowledgebase-btn" href="javaScript:void(0)"
                                        data-id="' . $knowledgebase->id . '" data-question="' . $knowledgebase->question . '" data-answer="' . $knowledgebase->answer . '"
                                        data-keywords="' . $knowledgebase->keywords . '" data-roles=' . $knowledgebase->roles->pluck('id')->toJson() . '>
                                                <span class="m-nav__link-text">Edit Question</span>
                                            </a>';
                    }
                    if ($this->auth_user->hasPermissionTo('delete_knowledgebase')) {
                        $actions .= '<li class="m-nav__item"><a data-table="dt-bs4-knowledge-base" class="m-nav__link btn-delete" href="' . route('knowledgebase.destroy', $knowledgebase->id) . '">
                                                <span class="m-nav__link-text">Delete knowledgebase</span>
                                            </a></li>';
                    }
                    $actions .= '</ul>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>';
                } else {
                    $actions = '<button class="btn btn-outline-dark btn-sm cursor"  id="actions" disabled>
                    <i class="la la-lock"></i></button>';
                }
                $actions .= '</div>';
                return $actions;
            })->rawColumns(['keywords', 'question', 'roles', 'actions'])->addIndexColumn()->make(true);

    }
    public function counters()
    {
       $TotalKb = KnowledgeBase::count();
       $AdminKb = KnowledgeBase::where(function($query){
        $query->whereHas('roles', function ($q) {
            $q->where('name', $this->auth_user->roles()->name='admin');
        });
       })->count();
        return response()->json([
           'total' => $TotalKb,
            'admin' => $AdminKb,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        KnowledgeBase::whereId($id)->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Question deleted successfully',
        ], 200);
    }
}
