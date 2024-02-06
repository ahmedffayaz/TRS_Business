<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use App\Models\KnowledgeBaseTopic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\KnowledgeBaseTopicRequest;

class KnowledgeBaseTopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $knowledgeBaseId = KnowledgeBase::findOrFail($request->id)->id;

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.topics.edit', compact('knowledgeBaseId'))->render()
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
    public function store(KnowledgeBaseTopicRequest $request)
    {
        try {
            DB::beginTransaction();
            KnowledgeBaseTopic::create([
                'knowledge_base_id' => $request->knowledge_base_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);

            DB::commit();

            if ($request->ajax()) {
                $url = route('dashboard.knowledge-base-topic.fetch-record', $request->knowledge_base_id);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge base topic created successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }

            Session::flash('success', 'Knowledge base topic created successfully.');
            return redirect()->route('dashboard.knowledge-base-topics.index');
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => 'Something went wrong.' . $exception->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            Session::flash('error', 'Something went wrong.');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        try {
            $topic = KnowledgeBaseTopic::whereKnowledgeBaseId($request->knowledge_base_id)
            ->whereHas('knowledgeBase')->with('knowledgeBase')->findOrFail($id);

            $knowledgeBaseId = $topic->knowledgeBase->id;

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.topics.edit', compact('topic', 'knowledgeBaseId'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong. ' . $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KnowledgeBaseTopicRequest $request, string $id)
    {
        try {
            DB::beginTransaction();
            $topic = KnowledgeBaseTopic::whereKnowledgeBaseId($request->knowledge_base_id)->findOrFail($id);
            $topic->update([
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('name'))
            ]);
            DB::commit();

            if ($request->ajax()) {
                $url = route('dashboard.knowledge-base-topic.fetch-record', $request->knowledge_base_id);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge base topic updated successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }

            Session::flash('success', 'Knowledge base topic updated successfully.');
            return redirect()->route('dashboard.knowledge-base-topics.index');
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => 'Something went wrong.' . $exception->getMessage()
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            Session::flash('error', 'Something went wrong.');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $topic = KnowledgeBaseTopic::whereKnowledgeBaseId($request->input('knowledge_base_id'))->findOrFail($id);

            // Delete topics and Questions
            $topic->qas->each(function ($query) {
                $query->delete();
            });

            // Delete knowledge base
            $topic->delete();
            DB::commit();

            // redirect url
            $url = route('dashboard.knowledge-base-topic.fetch-record', $topic->knowledgeBase->id);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'success' => 'Knowledge base topic deleted successfully.',
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

    public function fetchRecord($id)
    {
        try {
            $data = auth()->user()->hasRole('admin') ? KnowledgeBaseTopic::where('knowledge_base_id', $id)
            ->whereHas('knowledgeBase') : KnowledgeBaseTopic::where('knowledge_base_id', $id)
            ->whereHas('knowledgeBase', function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
            });

            $topics = $data->with('qas')->latest()->paginate(21);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' =>  view('backend.knowledge-base.topics.index-data', compact('topics'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong. ' . $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
            'knowledge_base_id' => 'required|integer'
        ]);

        try {
            // Get the search query from the request
            $search = $request->input('search');
            $knowledgeBaseId = $request->input('knowledge_base_id');

            // Perform the search query on both KnowledgeBaseTopic and KnowledgeBaseQa models
            $topics = KnowledgeBaseTopic::where('name', 'like', '%' . $search . '%')
                ->orWhereHas('qas', function ($query) use ($search) {
                    $query->where('question', 'like', '%' . $search . '%')
                    ->orWhere('answer', 'like', '%' . $search . '%')
                    ->orWhere('keywords', 'like', '%' . $search . '%');
                })->where('knowledge_base_id', $request->input('knowledge_base_id'))
                ->with('qas')->latest()->paginate(21);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' =>  view('backend.knowledge-base.topics.index-data', compact('topics', 'search', 'knowledgeBaseId'))->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'error' => 'Something went wrong.'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
