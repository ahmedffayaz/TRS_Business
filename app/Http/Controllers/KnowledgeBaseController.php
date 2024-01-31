<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Support\Str;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\KnowledgeBaseRequest;
use App\Models\KnowledgeBaseTopic;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.knowledge-base.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $roles = Role::all();
            $companies = Company::all();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.edit', compact('companies', 'roles'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong.'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KnowledgeBaseRequest $request)
    {
        try {
            if ($request->has('image')) {
                $imageName = Str::slug($request->image) . '_image_' . time() . '.' . $request->image->extension();
                $request->image->storeAs('public/images/knowledge-base', $imageName);
                $image = $imageName;
            }

            DB::beginTransaction();
            $knowledgeBase = KnowledgeBase::create([
                'created_by' => $request->created_by,
                'updated_by' => $request->updated_by,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'image' => isset($image) ? $image : null
            ]);

            // Attach selected companies to the knowledge base
            $knowledgeBase->companies()->attach($request->companies);
            // Attach selected roles to the knowledge base
            $knowledgeBase->roles()->attach($request->roles);

            DB::commit();
            $url = route('dashboard.knowledge-base.fetch-record');

            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge Base Created Successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }

            Session::flash('success', 'Knowledge Base Created Successfully.');
            return redirect()->route('dashboard.knowledge-bases.index');
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => $exception->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            Session::flash('error', $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $topics = KnowledgeBaseTopic::where('knowledge_base_id', $id)->get();
        return view('backend.knowledge-base.topics.index', compact('topics', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            $roles = Role::all();
            $companies = Company::all();
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.edit', compact('knowledgeBase', 'companies', 'roles'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong.'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeBaseRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $knowledgeBase = KnowledgeBase::findOrFail($id);

            if ($request->has('image')) {
                if (File::exists(public_path('storage/images/knowledge-base/' . $knowledgeBase->image)))
                    File::delete(public_path('storage/images/knowledge-base/' . $knowledgeBase->image));

                $imageName = Str::slug($request->image) . '_image_' . time() . '.' . $request->image->extension();
                $request->image->storeAs('public/images/knowledge-base', $imageName);
                $image = $imageName;
            }

            $knowledgeBase->update([
                'updated_by' => $request->updated_by,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'image' => isset($image) ? $image : $knowledgeBase->image
            ]);

            // Update selected companies to the knowledge base
            $knowledgeBase->companies()->sync($request->companies);
            // Update selected roles to the knowledge base
            $knowledgeBase->roles()->sync($request->roles);

            DB::commit();

            $url = route('dashboard.knowledge-base.fetch-record');

            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge base updated successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }

            Session::flash('success', 'Knowledge base updated successfully.');
            return redirect()->route('dashboard.knowledge-bases.index');
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => $exception->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            Session::flash('error', $exception->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $knowledgeBase = KnowledgeBase::findOrFail($id);

            // Delete topics and Questions
            $knowledgeBase->topics->each(function ($query) {
                $query->qas->each(function ($query) {
                    $query->delete();
                });
                $query->delete();
            });

            // Delete image
            if (File::exists(public_path('storage/images/knowledge-base/' . $knowledgeBase->image)))
                File::delete(public_path('storage/images/knowledge-base/' . $knowledgeBase->image));

            // Delete knowledge base
            $knowledgeBase->delete();
            DB::commit();

            // redirect url
            $url = route('dashboard.knowledge-base.fetch-record');

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'success' => 'Knowledge base deleted successfully.',
                'url' => $url
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong.'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function fetchRecord()
    {
        try {
            $data = auth()->user()->hasRole('admin')
            ? KnowledgeBase::with(['roles', 'topics', 'companies'])
            : KnowledgeBase::where(function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
            })->with(['topics', 'companies'])->orWhereDoesntHave('roles');

            $knowledgeBases = $data->latest()->paginate(21);
            $roles = Role::all();
            $companies = Company::all();

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' =>  view('backend.knowledge-base.index-data', compact('knowledgeBases', 'roles', 'companies'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong.'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
