<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Models\KnowledgeBase;

class NewKnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        return view('backend.knowledge-base.index', compact('roles', 'companies', 'knowledgeBases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.knowledge-base.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
