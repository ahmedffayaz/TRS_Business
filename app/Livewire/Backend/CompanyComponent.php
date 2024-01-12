<?php

namespace App\Livewire\Backend;

use App\Models\Company;
use App\Models\Country;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithOffcanvas;
use App\Livewire\Forms\CompanyForm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class CompanyComponent extends Component
{
    use WithPagination;
    use WithOffcanvas;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public CompanyForm $form;

    private function getCompanies(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return Company::with('country')->withCount('employees')
            ->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $companies = $this->getCompanies();
        $countries = Country::all();
        return view('livewire.backend.company-component', compact('companies', 'countries'));
    }

    public function store()
    {
        $validated = $this->form->validate();
        try {
            Company::create($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Company Created Successfully!']);
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $company = Company::findOrFail($id);
            $this->form->set($company);
            $this->openOffcanvas();
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the company could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();
        try {
            Company::findOrFail($id)->update($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Company Updated Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the company could not be found in our database.');
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
            'description' => 'You are about to delete the company. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $user = Company::findOrFail($id);
            $user->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Company Deleted Successfully!']);
        } catch (ModelNotFoundException $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Sorry, the company could not be found in our database.']);
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }
}

