<?php

namespace App\Livewire\Partials;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;

class CmsSidebarComponent extends Component
{
    public function render()
    {
        return view('livewire.partials.cms-sidebar-component');
    }
}
