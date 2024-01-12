<?php

namespace App\Livewire\Backend;

use App\Livewire\Forms\ContractForm;
use App\Models\Contract;
use App\Models\Role;
use App\Models\UserContract;
use App\Traits\WithMainModal;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;

class UserContractsComponent extends Component
{
    use WithPagination;
    use WithMainModal;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public ContractForm $form;

    private function getContracts(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        // getList($this->search, $this->columnName, $this->sortDirection)
        return Contract::paginate($this->limitPerPage);
    }

    #[Title('User Contracts')]
    public function render()
    {
        $contracts = $this->getContracts();
        $roles = Role::pluck('title', 'id')->all();
        return view('livewire.backend.user-contracts-component', compact('contracts', 'roles'));
    }

    public function store()
    {
        $this->form->validate();
        try {
            DB::beginTransaction();
            $contract = Contract::create([
                'uuid' => getUuid(),
                'title' => $this->form->title,
                'description' => $this->form->description,
                'version'=> '1',
            ]);
            $contract->roles()->sync($this->form->roles);
            DB::commit();
            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Contract Created Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $contract = Contract::findOrFail($id);
            $this->form->set($contract);
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function update($id)
    {
        $this->form->validate();
        $contract = Contract::with('roles')->findOrFail($id);

        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) use ($contract) {
                    $existingCount = DB::table('permissions')
                        ->where('title', $value)
                        ->where('id', '!=', $contract->id)
                        ->count();

                    if ($existingCount > 0) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ]
        ]);
        try {

            DB::beginTransaction();
            $version = $this->getVersion($id);
            $contract->update([
                'title' => $this->form->title,
                'description' => $this->form->description,
                'version' => $version,
            ]);
            $contract->roles()->sync($this->form->roles);
            DB::commit();

            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Contract Updated Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }


    public function getVersion($contract_id)
    {
       $contract = Contract::findOrFail($contract_id);
        return $contract->version += 0.1;
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
            $contract = UserContract::findOrFail($id);
            $contract->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Contract Deleted Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }
}
