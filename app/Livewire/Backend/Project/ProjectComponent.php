<?php

namespace App\Livewire\Backend\Project;

use Exception;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\ProjectForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\On;

#[Title('Projects')]
class ProjectComponent extends Component
{
    use WithPagination, WithMainModal;

    public $business_id;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 10;

    public ProjectForm $form;

    public function mount()
    {
        $this->business_id = Business::whereName(session('business'))->first()->id;
    }

    private function getProjectQuery()
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return Project::sessionBusiness()->with(['client', 'members', 'tasks'])->withCount(['members', 'tasks'])
            ->getList($this->search, $this->columnName, $this->sortDirection);
    }

    private function getTotalProjects() : LengthAwarePaginator
    {
        return $this->getProjectQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveProjects() : LengthAwarePaginator
    {
        return $this->getProjectQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedProjects() : LengthAwarePaginator
    {
        return $this->getProjectQuery()->onlyTrashed()->paginate($this->limitPerPage);
    }

    private function getProjects(): LengthAwarePaginator
    {
        if ($this->dataCountType === 'total')
            return $this->getTotalProjects();
        else if ($this->dataCountType === 'active')
            return $this->getActiveProjects();
        else if ($this->dataCountType === 'archived')
            return $this->getArchivedProjects();
    }

    public function render()
    {
        $projects = $this->getProjects();
        $totalProjects = Project::sessionBusiness()->withTrashed()->count();
        $activeProjects = Project::sessionBusiness()->count();
        $archivedProjects = Project::sessionBusiness()->onlyTrashed()->count();
        $clients = Client::select('id', 'business_id', 'name')->sessionBusiness()->get();
        $members = User::sessionBusiness()->usersWithoutClientRole()->get();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.project.project-component', compact('projects', 'totalProjects', 'activeProjects', 'archivedProjects', 'clients', 'members'));
    }

    public function closeModal()
    {
        $this->dispatch('close-main-modal');
        $this->form->reset();
        $this->resetValidation();
        $this->dispatch('client-select', ['formClient' => []]);
        $this->dispatch('currency-select', ['formCurrency' => []]);
        $this->dispatch('project-auto-archive-status-select', ['formIsAutoArchived' => []]);
        $this->dispatch('project-members-select', ['formMembers' => []]);
    }

    public function store()
    {
        $this->form->business_id = $this->business_id;
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            $project = Project::create([
                'business_id' => $validated['business_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'type' => $validated['type'],
                'nature' => $validated['nature'],
                'budget' => $validated['budget'],
                'hourly_rate' => $validated['hourly_rate'],
                'currency' => $validated['currency'],
                'status' => $validated['status'],
                'is_auto_archive' => $validated['is_auto_archive'],
                'reports_schedule' => !empty($validated['reports_schedule']) ? implode(',', $validated['reports_schedule']) : null
            ]);

            if ($validated['members']) $project->members()->attach($validated['members']);

            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Project created successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while create project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->business_id = $this->business_id;
        $this->form->id = $id;

        try {
            $project = Project::with('members')->findOrFail($id);
            $this->form->set($project);

            $this->dispatch('client-select', ['formClient' => $project->client_id]);
            $this->dispatch('currency-select', ['formCurrency' => $project->currency]);
            $this->dispatch('project-auto-archive-status-select', ['formIsAutoArchived' => $project->is_auto_archived]);

            // Set the selected roles in the form
            $this->form->members = $project->members->pluck('id')->toArray();
            $this->dispatch('project-members-select', ['formMembers' => $this->form->members]);

            $this->openMainModal();
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error while edit project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();
        // dd($validated);

        try {
            $project = Project::sessionBusiness()->findOrFail($id);
            DB::beginTransaction();
            $project->update([
                'business_id' => $validated['business_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'type' => $validated['type'],
                'nature' => $validated['nature'],
                'budget' => $validated['budget'],
                'hourly_rate' => $validated['hourly_rate'],
                'currency' => $validated['currency'],
                'status' => $validated['status'],
                'is_auto_archive' => $validated['is_auto_archive'],
                'reports_schedule' => !empty($validated['reports_schedule']) ? implode(',', $validated['reports_schedule']) : null
            ]);

            if ($validated['members']) $project->members()->sync($validated['members']);

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Project updated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the project.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $project = Project::sessionBusiness()->findOrFail($id);
            $project->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Knowledge base category deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while delete project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while delete project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
