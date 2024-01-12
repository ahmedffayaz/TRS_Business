<?php

namespace App\Livewire\Backend;

use Livewire\Attributes\Title;
use Livewire\Component;

class DashboardComponent extends Component
{

    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.backend.dashboard-component');
    }
}
