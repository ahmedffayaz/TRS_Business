<?php

namespace App\Livewire\Backend;

use App\Models\KnowledgeBase;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class KnowledgeBaseComponent extends Component
{
    use WithPagination;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public $knowledgeBaseData;
    public $roles;
    
    public $id;
    public $question;
    public $answer;
    public $keywords;
    public $isOpen = 0;

    protected $rules = [
        'knowledgeBaseData.id' => 'nullable|exists:knowledge_base,id',
        'knowledgeBaseData.question' => 'required',
        'knowledgeBaseData.answer' => 'required',
        'knowledgeBaseData.keywords' => 'required',
    ];

    public function mount()
    {
        $this->roles = Role::all();
        $this->knowledgeBaseData = $this->getRecord();
    }

    public function getRecord()
    {
        if (!$this->auth_user->hasRole('admin')) {
            abort(403);
        }

        $data = $this->auth_user->hasRole('admin')
            ? KnowledgeBase::get()
            : KnowledgeBase::where(function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
            })->orWhereDoesntHave('roles')->get();

        return $data->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        return view('livewire.backend.knowledge-base-component');
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->id = '';
        $this->question = '';
        $this->answer = '';
        $this->keywords = '';
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->knowledgeBaseData['id']) {
                $knowledgeBase = KnowledgeBase::with(['roles'])->findOrFail($this->knowledgeBaseData['id']);
                $knowledgeBase->update($this->knowledgeBaseData);
            } else {
                $knowledgeBase = KnowledgeBase::create($this->knowledgeBaseData);
            }

            $knowledgeBase->roles()->sync($this->knowledgeBaseData['select_role']);

            DB::commit();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Question ' . ($this->knowledgeBaseData['id'] ? 'updated' : 'added') . ' successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', ['type' => 'error',  'message' => $e->getMessage()]);
        }
    }

    public function counters()
    {
        $totalKb = KnowledgeBase::count();
        $adminKb = KnowledgeBase::whereHas('roles', function ($q) {
            $q->where('name', 'admin');
        })->count();

        return response()->json([
            'total' => $totalKb,
            'admin' => $adminKb,
        ], 200);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            KnowledgeBase::findOrFail($id)->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Question deleted successfully']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }
}
