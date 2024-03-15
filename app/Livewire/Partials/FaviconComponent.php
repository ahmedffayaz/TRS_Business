<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Models\Business;

class FaviconComponent extends Component
{
    private function getSessionBusiness()
    {
        return Business::whereName(session('business'))->first();
    }

    public function render()
    {
        $business = $this->getSessionBusiness();
        return view('livewire.partials.favicon-component', compact('business'));
    }
}
