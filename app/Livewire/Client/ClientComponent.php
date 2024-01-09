<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use Livewire\Component;
use App\Models\Currency;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithOffcanvas;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientComponent extends Component
{
    use WithPagination;
    use WithOffcanvas;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public ClientForm $form;

    private function getClients(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return Client::with(['company', 'country'])
            ->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $companies = Company::all();
        $countries = Country::all();
        $rateUnits = Currency::get(['code']);
        $clients = $this->getClients();
        return view('livewire.client.client-component', compact('companies', 'countries', 'rateUnits', 'clients'));
    }

    public function store()
    {
        $validated = $this->form->validate();
        try {
            Client::create($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Client Created Successfully!']);
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $client = Client::findOrFail($id);
            $this->form->set($client);
            $this->openOffcanvas();
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the client could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();
        try {
            Client::findOrFail($id)->update($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Client Updated Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the client could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
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
            $user = Client::findOrFail($id);
            $user->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Client Deleted Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the client could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }
}
