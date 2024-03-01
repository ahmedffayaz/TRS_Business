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

    public function mount()
    {

        $this->business = Business::whereName(session('business'))->first();
        $this->form->business_id = $this->business->id;
    }

    private function getClients(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return Client::whereHas('business', function ($query) {
            $query->whereName(session('business'));
        })->with(['business', 'country'])->withCount('employees')
        ->getList($this->search, $this->columnName, $this->sortDirection)
        ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $countries = Country::all();
        $rateUnits = Currency::get(['code']);
        $clients = $this->getClients();

        return view('livewire.backend.client.client-component', compact('countries', 'rateUnits', 'clients'));
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
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Client Deleted Successfully!']);
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

    public function addUserFields()
    {
        try {
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'html' => view('livewire.backend.client.user-fields')->render()
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            Log::error('Get error while add user fields on adding or updating clients: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong']);
        }
    }
}
