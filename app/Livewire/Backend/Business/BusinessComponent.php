<?php

namespace App\Livewire\Backend\Business;

use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Select Business')]
class BusinessComponent extends Component
{
    use WithPagination;

    public $user, $limitPerPage = 15;

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function render()
    {
        $businesses = $this->getBusinesses();
        return view('livewire.backend.business.business-component', compact('businesses'));
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

        return redirect()->route('dashboard');
    }
}
