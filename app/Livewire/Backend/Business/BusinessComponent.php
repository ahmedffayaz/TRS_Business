<?php

namespace App\Livewire\Backend\Business;

use App\Models\Business;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Select Business')]
class BusinessComponent extends Component
{
    public $user;
    public function mount()
    {
        $this->user = auth()->user();
    }

    public function render()
    {
        $businesses = $this->getBusinesses();
        return view('livewire.backend.business.business-component', compact('businesses'));
    }

    private function getBusinesses()
    {
        $query = Business::select('id', 'name', 'slug', 'logo', 'created_at');
        $data = $this->user->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function($query) {
                    $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
        return $data->get();
    }

    public function selectBusiness($name)
    {
        session(['business' => $name]);

        return redirect()->route('dashboard');
    }
}
