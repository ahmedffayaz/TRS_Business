<?php

namespace App\Livewire\Backend\Project;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\ProjectForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    public array $roles = [];
    public $slug = null;
    public $encryption;
    public $show_swl = false;

    public function mount()
    {
        $this->business_id = Business::whereName(session('business'))->first()->id;
        $this->encryption = session('swl_key');
        session()->forget('swl_key');
        if($this->encryption){
            $this->show_swl = true;
        }
    }

    private function getProjectQuery()
    {
        $user = auth()->user();
        $this->dispatch('reinitialize-icons');
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        $this->dispatch('reinitialize-chart');
        return Project::sessionBusiness()->when(!$user->can('view_total_projects'), function ($query) use ($user) {
                $query->whereHas('members', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
            })->with(['client', 'members', 'tasks'])->withCount(['members', 'tasks'])
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
        $user = auth()->user();
        $projects = $this->getProjects();
        $totalProjects =  $this->getTotalProjects()->count();
        $activeProjects = $this->getActiveProjects()->count();
        $archivedProjects = Project::sessionBusiness()->onlyTrashed()->count();
        $clients = Client::select('id', 'business_id', 'name')->sessionBusiness()->get();
        $members = User::sessionBusiness()->get();
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
        $this->dispatch('reinitialize-icons');
        $this->dispatch('reinitialize-chart');
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
                'slug' => Str::slug($validated['name']),
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
            $this->dispatch('reinitialize-icons');
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

        try {
            $project = Project::sessionBusiness()->findOrFail($id);
            DB::beginTransaction();
            $project->update([
                'business_id' => $validated['business_id'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
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
            $this->dispatch('reinitialize-icons');
            $this->dispatch('reinitialize-chart');
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
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Project deleted successfully.']);
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

    public function restoreConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'restore',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to restore the project.',
        ]);
    }
    #[On('restore')]
    public function restore($id)
    {
        try {
            DB::beginTransaction();
            $project = Project::withTrashed()->sessionBusiness()->findOrFail($id);
            $project->restore();
            DB::commit();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Project restored successfully.']);
                $this->dispatch('reinitialize-chart');
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while restore project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while restore project: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    #[On('reject_invite_link')]
    public function rejectInvitation()
    {
        session()->flash('error', 'You rejected this invitation.');
        return redirect()->route('dashboard.projects.index');
    }

    #[On('accept_invite_link')]
    public function acceptInvitation()
    {
        try {
        $decryption = Crypt::decrypt($this->encryption);
        if (Auth::check()) {
            $user = Auth::user();
            $project = Project::where('client_id', $decryption['client_id'])
                ->where('business_id', $decryption['business_id'])
                ->where('slug', $decryption['slug'])->with('members')->firstOrFail();

            $project->members()->attach($user->id);
            session()->flash('status', 'Now you are member of project.');
        }

       }catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        session()->flash('error', 'Invalid data. Please try again.');
       }
    }
}
