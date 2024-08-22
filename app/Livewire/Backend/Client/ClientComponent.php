<?php

namespace App\Livewire\Backend\Client;

use Exception;
use App\Models\Client;
use App\Models\Country;
use Livewire\Component;
use App\Models\Business;
use App\Models\Currency;
use Livewire\Attributes\On;
use App\Traits\WithMainModal;
use Livewire\Attributes\Title;
use App\Livewire\Forms\ClientForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Livewire\WithPagination;

#[Title('Clients')]
class ClientComponent extends Component
{
    use WithMainModal, WithPagination;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public $business;
    public $clientDetail;

    public ClientForm $form;
    public $countryId = '';
    public $selected_company = '';

    public function mount()
    {

        $this->business = Business::whereName(session('business'))->first();
        $this->form->business_id = $this->business->id;
    }

    private function getClients(): LengthAwarePaginator
    {
        $user = auth()->user();
        $this->search ? $this->resetPage() : '';
        return Client::whereHas('business', function ($query) {
            $query->whereName(session('business'));
        })
            ->when($user->hasPermissionTo('view_clients') && $user->hasRole('client') && !$user->hasRole('super-admin'), function ($query) use ($user) {
                $query->whereHas('employees', function ($query) use ($user) {
                    $query->whereId($user->id);
                });
            })
            ->when($this->countryId, function ($query) {
                $query->where('country_id', $this->countryId);
            })
            ->when($this->selected_company, function ($query) {
                $query->where('id', $this->selected_company);
            })
            ->with(['business', 'country', 'employees'])
            ->withCount('employees')
            ->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $countries = Country::all();
        $rateUnits = Currency::get(['code']);
        $clients = $this->getClients();
        $all_clients = Client::sessionBusiness()->orderBy('name', 'asc')->get(['id', 'name']);
        $countryIds = Client::sessionBusiness()->pluck('country_id')->toArray();
        $allCountries  = Country::whereIn('id', $countryIds)->orderBy('name', 'asc')->get(['id', 'name']);
        $this->dispatch('reinitialize-select-container');
        // $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.client.client-component', compact('countries', 'rateUnits', 'clients','all_clients','allCountries'));
    }

    public function show($slug)
    {
        try {
            $this->clientDetail = Client::sessionBusiness()->whereSlug($slug)
                ->with(['employees', 'business', 'country'])->withCount('employees')->firstOrFail();
            $this->dispatch('open-main-modal');
        } catch (ModelNotFoundException $exception) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the client could not be found in our database.'
            ]);
        } catch (Exception $exception) {
            Log::error('Get error while displaying client detail: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the client. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $user = Client::whereHas('business', function ($query) {
                $query->whereName(session('business'));
            })->findOrFail($id);
            $user->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Client Deleted Successfully!']);
        } catch (ModelNotFoundException $exception) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the client could not be found in our database.'
            ]);
        } catch (Exception $exception) {
            Log::error('Get error while delete client: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong']);
        }
    }
    #[On('reset-filter')]
    public function resetFilters()
    {
        $this->reset(['countryId', 'selected_company', 'search']);
        $this->render();
    }
    #[On('apply-filter')]
    public function applyFilter($countryFilter, $selectedCompany)
    {
        $this->countryId = $countryFilter;
        $this->selected_company = $selectedCompany;
        $this->render();
    }
}
