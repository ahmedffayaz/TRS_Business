<?php

namespace App\Livewire\Backend\Project;

use App\Models\Project;
use App\Traits\WithMainModal;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Project Details')]
class ProjectDetailComponent extends Component
{
    use WithMainModal;

    public string $slug;
    public bool $isRevenueModalOpen = false;
    public bool $isTaskModalOpen = false;

    public function mount($slug)
    {
        $this->slug = $slug;
    }

    private function getProject()
    {
        return Project::sessionBusiness()->whereSlug($this->slug)
            ->with(['tasks', 'client', 'members' => function ($query) {
                $query->with(['roles']);
            }])->withCount(['tasks', 'members'])->firstOrFail();
    }

    public function render()
    {
        $project = $this->getProject();
        return view('livewire.backend.project.project-detail-component', compact('project'));
    }

    public function showRevenueModal()
    {
        $this->isRevenueModalOpen = true;

        try {
            $projectRevenue = Project::sessionBusiness()->whereSlug($this->slug)->withTrashed()
                ->with(['members'])->firstOrFail();

            $this->openMainModal();
        } catch (Exception $exception) {
            Log::error('Get error while get project revenue: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
