<?php

namespace App\Livewire\Backend\Cms\Dashboard;

use Livewire\Component;

class CmsDashboardComponent extends Component
{
    public $user;
    public function mount()
    {
        $this->user = auth()->user();
    }
    public function render()
    {
        return view('livewire.backend.cms.dashboard.cms-dashboard-component');
    }
}
