<?php

namespace App\Livewire\Backend;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithOffcanvas;
use App\Livewire\Forms\ProjectForm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Title;

#[Title('Projects')]
class ProjectComponent extends Component
{
    use WithPagination;
    use WithOffcanvas;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public ProjectForm $form;

    private function getProjects(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return Project::getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        return view('livewire.backend.project-component');
    }

    public function store()
    {
        $validated = $this->form->validate();
        try {
            Project::create($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Project Created Successfully!']);
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $client = Project::findOrFail($id);
            $this->form->set($client);
            $this->openOffcanvas();
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the project could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }
}
