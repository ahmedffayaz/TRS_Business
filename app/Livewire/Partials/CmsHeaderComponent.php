<?php

namespace App\Livewire\Partials;

use App\Models\Business;
use Livewire\Component;

class CmsHeaderComponent extends Component
{
    public $businessId, $user, $businesses;
    public function mount()
    {
        $this->getBusinesses();
    }

    public function getBusinessState()
    {
        session()->forget('business');
        $this->getBusinesses();
        return redirect()->route('dashboard.home');
    }
    private function getBusinesses()
    {
        $this->user = auth()->user();
        $business = $this->user->hasRole('super-admin')
            ? Business::orderBy('id', 'asc')
            : Business::whereHas('roles', function ($query) {
                $query->whereHas('users', function ($query) {
                    $query->where('users.id', $this->user->id);
                });
            })->orderBy('id', 'asc');

        if (!empty($this->businessId)) {
            $business = $business->whereId($this->businessId);
            $requestBusiness = $business->first();
            session()->forget('business');
            session(['business' => $requestBusiness->name]);
            $this->businesses = $business->pluck('name', 'id')->toArray();
        } else {
            $this->businesses = $business->pluck('name', 'id')->toArray();
        }

        $sessionBusiness = session('business');
        $filterResult = array_filter($this->businesses, function ($key) use ($sessionBusiness) {
            return $key == $sessionBusiness;
        });

        foreach ($filterResult as $key => $value) {
            $this->businessId = $key;
        }
        return $this->businesses;
    }
    public function render()
    {
        $businesses = $this->businesses;
        return view('livewire.partials.cms-header-component', compact('businesses'));
    }
}
