<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\KnowledgeBaseQa;
use Illuminate\Http\JsonResponse;
use App\Models\KnowledgeBaseTopic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\KnowledgeBaseQuestionRequest;

class KnowledgeBaseQuestionController extends Controller
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
            $knowledgeBaseTopicId = KnowledgeBaseTopic::findOrFail($request->input('knowledge_base_topic_id'))->id;

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.questions.edit', compact('knowledgeBaseTopicId'))->render()
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
    public function store(KnowledgeBaseQuestionRequest $request)
    {
        try {
            DB::beginTransaction();
            $question = KnowledgeBaseQa::create([
                'knowledge_base_topic_id' => $request->input('knowledge_base_topic_id'),
                'question' => $request->input('question'),
                'slug' => Str::slug($request->input('question')),
                'answer' => $request->input('description'),
                'keywords' => $request->input('keywords')
            ]);

            DB::commit();

            if ($request->ajax()) {
                $url = route('dashboard.knowledge-base-topic.fetch-record', $question->topic->knowledgeBase);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge base question created successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => 'Something went wrong.'
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
        $question = KnowledgeBaseQa::findOrFail($id);
        return view('backend.knowledge-base.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        try {
            $question = KnowledgeBaseQa::whereKnowledgeBaseTopicId($request->knowledge_base_topic_id)
            ->whereHas('topic')->with('topic')->findOrFail($id);

            $knowledgeBaseTopicId = $question->topic->id;

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'data' => view('backend.knowledge-base.questions.edit', compact('question', 'knowledgeBaseTopicId'))->render()
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
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $question = KnowledgeBaseQa::where('knowledge_base_topic_id', $request->input('knowledge_base_topic_id'))->findOrFail($id);
            $question->update([
                'question' => $request->input('question'),
                'slug' => Str::slug($request->input('question')),
                'answer' => $request->input('description'),
                'keywords' => $request->input('keywords')
            ]);

            DB::commit();

            if ($request->ajax()) {
                $url = route('dashboard.knowledge-base-topic.fetch-record', $question->topic->knowledgeBase);
                return response()->json([
                    'status' => JsonResponse::HTTP_OK,
                    'success' => 'Knowledge base question updated successfully.',
                    'url' => $url
                ], JsonResponse::HTTP_OK);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                    'error' => 'Something went wrong.'
                ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
            Session::flash('error', 'Something went wrong.');
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
            $question = KnowledgeBaseQa::findOrFail($id);

            // Delete knowledge base
            $question->delete();
            DB::commit();

            // redirect url
            $url = route('dashboard.knowledge-base-topic.fetch-record', $question->topic->knowledgeBase->id);

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'success' => 'Knowledge base question deleted successfully.',
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
}
