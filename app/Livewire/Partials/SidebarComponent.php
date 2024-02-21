<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Models\Business;
use Livewire\Attributes\On;

class SidebarComponent extends Component
{
    public function render()
    {
        $business = Business::whereName(session('business'))->first();
        return view('livewire.partials.sidebar-component', compact('business'));
    }
}
