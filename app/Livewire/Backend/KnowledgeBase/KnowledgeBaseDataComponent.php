<?php

namespace App\Livewire\Backend\KnowledgeBase;

use Exception;
use App\Models\Role;
use App\Models\Keyword;
use Livewire\Component;
use App\Models\Business;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\KnowledgeBase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\KnowledgeBaseCategory;
use App\Livewire\Forms\KnowledgeBaseForm;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Livewire\Forms\KnowledgeBaseCategoryForm;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class KnowledgeBaseDataComponent extends Component
{
    use WithPagination;

    public $business_id;
    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 15;

    public bool $isQuestionModalOpen = false;
    public KnowledgeBaseCategoryForm $categoryForm;
    public KnowledgeBaseForm $questionForm;

    public bool $isShowKnowledgeBase = false;
    public $knowledgeBaseDetail;

    public bool $isKeywordSearch = false;
    public $keyword;

    protected $listeners = [
        'openKnowledgeBaseCategoryModal' => 'create',
        'openCreateKnowledgeBaseModal' => 'createKnowledgeBase'
    ];

    public function mount()
    {
        $this->business_id = Business::whereName(session('business'))->first()->id;
        $this->isKeywordSearch = request()->routeIs('dashboard.knowledgebase.search-keyword') ? true : false;
    }

    private function knowledgeBaseCategories() : LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        $query = KnowledgeBaseCategory::select('id', 'business_id', 'created_by', 'updated_by', 'name', 'created_at')
                ->sessionBusiness();
        $data = auth()->user()->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function ($query) {
                $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
            })->orWhere(function ($query) {
                $query->whereDoesntHave('roles');
            });

        $paginatedData = $data->with('questions')->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
        return $paginatedData;
    }

    private function knowledgeBaseKeywordCategories() : LengthAwarePaginator
    {
        $keyword = $this->keyword;
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        $query = KnowledgeBaseCategory::select('id', 'business_id', 'created_by', 'updated_by', 'name', 'created_at')
                ->sessionBusiness();
        $data = auth()->user()->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function ($query) {
                $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
            })->orWhere(function ($query) {
                $query->whereDoesntHave('roles');
            });

        $paginatedData = $data->whereHas('questions', function ($query) use ($keyword) {
            $query->whereHas('keywords', function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%');
            });
        })
        ->getList($this->search, $this->columnName, $this->sortDirection)
        ->with(['questions', 'questions.keywords'])->paginate($this->limitPerPage);
        return $paginatedData;
    }

    public function render()
    {
        $roles = Role::all();
        $knowledgeBaseCategories = $this->isKeywordSearch ? $this->knowledgeBaseKeywordCategories() : $this->knowledgeBaseCategories();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.knowledge-base.knowledge-base-data-component', compact('roles', 'knowledgeBaseCategories'));
    }

    private function openModal()
    {
        $this->dispatch('resetSelectInput');
        $this->dispatch('open-main-modal');
    }

    public function closeCategoryModal()
    {
        $this->dispatch('close-main-modal');
        $this->categoryForm->reset();
        $this->resetValidation();
        $this->dispatch('roles-select', ['formRoles' => []]);
    }

    public function create()
    {
        $this->isQuestionModalOpen = false;
        $this->categoryForm->isUpdate = false;
        $this->openModal();
    }

    public function store()
    {
        $this->categoryForm->business_id = $this->business_id;
        $this->categoryForm->created_by = auth()->user()->id;
        $this->categoryForm->updated_by = auth()->user()->id;

        $validated = $this->categoryForm->validate();

        try {
            DB::beginTransaction();
            $knowledgebaseCategory = KnowledgeBaseCategory::create([
                'business_id' => $validated['business_id'],
                'created_by' => $validated['created_by'],
                'updated_by' => $validated['updated_by'],
                'name' => $validated['name']
            ]);

            $knowledgebaseCategory->roles()->attach($validated['roles']);

            DB::commit();
            $this->closeCategoryModal();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base category created successfully.'
            ]);
            $this->dispatch('roles-select', ['formRoles' => []]);
            $this->dispatch('backend.knowledge-base.knowledge-base-category-data-component');
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $this->isQuestionModalOpen = false;
            $this->categoryForm->isUpdate = true;
            $knowledgebaseCategory = KnowledgeBaseCategory::sessionBusiness()->with('roles')->findOrFail($id);
            $this->categoryForm->setKnowledgeBaseCategory($knowledgebaseCategory);

            // Set the selected roles in the form
            $this->categoryForm->roles = $knowledgebaseCategory->roles->pluck('id')->toArray();
            $this->dispatch('roles-select', ['formRoles' => $this->categoryForm->roles]);

            $this->openModal();
        } catch (Exception $exception) {
            Log::error('Get error while open knowledge base category edit modal: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }

    }

    public function update($id)
    {
        $this->categoryForm->business_id = $this->business_id;
        $this->categoryForm->updated_by = auth()->user()->id;

        $validated = $this->categoryForm->validate();

        try {
            DB::beginTransaction();
            $knowledgebaseCategory = KnowledgeBaseCategory::sessionBusiness()->with('roles')->findOrFail($id);
            $knowledgebaseCategory->update([
                'updated_by' => $validated['updated_by'],
                'name' => $validated['name']
            ]);

            $knowledgebaseCategory->roles()->sync($validated['roles']);
            DB::commit();
            $this->closeCategoryModal();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base category updated successfully.'
            ]);
            $this->dispatch('roles-select', ['formRoles' => []]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }

    }

    public function deleteCategoryConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the knowledge base category. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $knowledgeBase = KnowledgeBaseCategory::findOrFail($id);
            $knowledgeBase->delete();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base category deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error while delete knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the Knowledge base category could not be found in our database.']);
        } catch (Exception $exception) {
            Log::error('Get error while delete knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.']);
        }
    }

    public function closeQuestionModal()
    {
        $this->dispatch('close-main-modal');
        $this->isQuestionModalOpen = false;
        $this->questionForm->reset();
        $this->resetValidation();
        $this->dispatch('category-select', ['formCategory' => []]);
    }

    public function createKnowledgeBase()
    {
        $this->isQuestionModalOpen = true;
        $this->questionForm->isUpdate = false;
        $this->openModal();
    }

    public function storeKnowledgeBase()
    {
        $validated = $this->questionForm->validate();

        try {
            DB::beginTransaction();
            $knowledgeBase = KnowledgeBase::create($validated);

            if (!empty($validated['keywords'])) {
                $keywordIds = $this->validatedKeywords($validated['keywords']);
                $knowledgeBase->keywords()->attach($keywordIds);
            }

            DB::commit();
            $this->closeQuestionModal();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base created successfully.'
            ]);
            $this->dispatch('category-select', ['formCategory' => []]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create knowledge base: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    public function showKnowledgeBase($id, $categoryId)
    {
        $this->isShowKnowledgeBase = true;
        try {
            $knowledgeBase = KnowledgeBase::where('knowledge_base_category_id', $categoryId)
                ->with(['category', 'keywords'])->findOrFail($id);
            $this->knowledgeBaseDetail = $knowledgeBase;
            $this->dispatch('resetMte');
            $this->dispatch('open-main-modal');
        } catch (Exception $exception) {
            Log::error('Get error while open knowledge base detail modal: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    public function closeKnowledgeBaseModal()
    {
        $this->isShowKnowledgeBase = false;
        $this->dispatch('close-main-modal');
    }

    public function editKnowledgeBase($id, $categoryId)
    {
        try {
            $this->isQuestionModalOpen = true;
            $this->questionForm->isUpdate = true;
            $knowledgeBase = KnowledgeBase::where('knowledge_base_category_id', $categoryId)->with('keywords')->findOrFail($id);
            $this->questionForm->set($knowledgeBase);
            $this->dispatch('roles-select', ['formCategory' => $this->questionForm->knowledge_base_category_id]);

            $this->openModal();
        } catch (Exception $exception) {
            Log::error('Get error while open knowledge base edit modal: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    public function updateKnowledgeBase($id)
    {
        $validated = $this->questionForm->validate();

        try {
            DB::beginTransaction();
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            $knowledgeBase->update($validated);

            if (!empty($validated['keywords'])) {
                $keywordIds = $this->validatedKeywords($validated['keywords']);
                $knowledgeBase->keywords()->sync($keywordIds);
            }
            DB::commit();
            $this->closeQuestionModal();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base updated successfully.'
            ]);
            $this->dispatch('category-select', ['formCategory' => []]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update knowledge base: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }

    private function validatedKeywords($validatedKeywords)
    {
        $keywords = preg_split('/\s*,\s*/', $validatedKeywords, -1, PREG_SPLIT_NO_EMPTY);
        $keywordIds = [];
        foreach($keywords as $keyword) {
            // Check if the keyword already exists in the database
            $existingKeyword = Keyword::where('name', $keyword)->first();

            if ($existingKeyword) {
                $keywordIds[] = $existingKeyword->id;
            } else {
                // If the keyword doesn't exist, create a new one
                $keywordModel = Keyword::create(['name' => $keyword]);
                $keywordIds[] = $keywordModel->id;
            }
        }
        return $keywordIds;
    }

    public function deleteKnowledgeBaseConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'knowledgeBaseDelete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the knowledge base. This action cannot be undone.'
        ]);
    }

    #[On('knowledgeBaseDelete')]
    public function destroyKnowledgeBase($id)
    {
        try {
            $knowledgeBase = KnowledgeBase::findOrFail($id);
            $knowledgeBase->delete();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base category deleted successfully.'
            ]);
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error while delete knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the Knowledge base category could not be found in our database.']);
        } catch (Exception $exception) {
            Log::error('Get error while delete knowledge base category: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.'
            ]);
        }
    }
}
