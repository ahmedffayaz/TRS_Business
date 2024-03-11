<?php

namespace App\Livewire\Backend\User;

use Livewire\Component;
use App\Models\Business;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\TermsConditionUser;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('My Contracts')]
class UserContractComponent extends Component
{
    use WithPagination;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public $business;

    public function mount()
    {
        $this->business = Business::whereName(session('business'))->first();
    }

    private function getUserContracts() : LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching

        return TermsConditionUser::whereHas('user')->with('termsCondition')
        ->getList($this->search, $this->columnName, $this->sortDirection)
        ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $contracts = $this->getUserContracts();
        return view('livewire.backend.user.user-contract-component', compact('contracts'));
    }

    public function view($id)
    {
        $pdf = TermsConditionUser::select('id', 'pdf_url')->findOrFail($id);
        $path = URL::to('/') . '/' . $pdf->pdf_url;
        return redirect()->away($path);
    }
}
