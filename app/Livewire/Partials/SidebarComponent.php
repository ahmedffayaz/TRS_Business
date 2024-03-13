<?php

namespace App\Livewire\Partials;

use Livewire\Component;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SidebarComponent extends Component
{
    public function mount()
    {
        if (empty($this->getSessionBusiness())) {
            Auth::logout();
            session()->forget('business');
            session()->flash('error', 'Session expired.');
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Account not active, Please contact admin.']);
            return Redirect::route('login');
        }
    }

    private function getSessionBusiness()
    {
        return Business::whereName(session('business'))->first();
    }

    public function render()
    {
        $business = $this->getSessionBusiness();
        return view('livewire.partials.sidebar-component', compact('business'));
    }
}
