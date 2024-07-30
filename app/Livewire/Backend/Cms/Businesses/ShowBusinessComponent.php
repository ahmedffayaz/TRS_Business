<?php

namespace App\Livewire\Backend\Cms\Businesses;

use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Business;
class ShowBusinessComponent extends Component
{
    public $user, $limitPerPage = 15;
    public function mount()
    {
        $this->user = auth()->user();
    }
    private function getBusinesses(): LengthAwarePaginator
    {
        $query = Business::select('id', 'name', 'slug', 'logo', 'created_at');
        $data = $this->user->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function($query) {
                    $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
                });
        return $data->paginate($this->limitPerPage);
    }

    public function selectBusiness($name)
    {
        session(['business' => $name]);

        return redirect()->route('dashboard.home');
    }
    public function render()
    {
        $businesses = $this->getBusinesses();
        return view('livewire.backend.cms.businesses.show-business-component' ,compact('businesses'));
    }
}
