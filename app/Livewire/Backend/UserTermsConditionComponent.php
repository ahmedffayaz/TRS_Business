<?php

namespace App\Livewire\Backend;

use App\Livewire\Forms\TermsConditionForm;
use App\Models\TermsCondition;
use App\Models\Role;
use App\Models\TermConditionUser;
use App\Traits\WithMainModal;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;

class UserTermsConditionComponent extends Component
{
    use WithPagination;
    use WithMainModal;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public TermsConditionForm $form;

    private function getTermsConditions(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        // getList($this->search, $this->columnName, $this->sortDirection)
        return TermsCondition::paginate($this->limitPerPage);
    }

    #[Title('User Terms & Conditions')]
    public function render()
    {
        $termsConditions = $this->getTermsConditions();
        $roles = Role::pluck('title', 'id')->all();
        return view('livewire.backend.user-terms-condition-component', compact('termsConditions', 'roles'));
    }
    
    // public function hydrate()
    // {
    //     $this->dispatch('select-container', ['formRole' => $this->form->roles]);
    // }

    public function store()
    {
        $this->form->validate();
        try {
            DB::beginTransaction();
            $contract = TermsCondition::create([
                'uuid' => getUuid(),
                'title' => $this->form->title,
                'description' => $this->form->description,
                'version' => '1',
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
            $contract = TermsCondition::findOrFail($id);
            $this->form->set($contract);
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function update($id)
    {
        $this->form->validate();
        $termsCondition = TermsCondition::with('roles')->findOrFail($id);

        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) use ($termsCondition) {
                    $existingCount = DB::table('permissions')
                        ->where('title', $value)
                        ->where('id', '!=', $termsCondition->id)
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
            $termsCondition->update([
                'title' => $this->form->title,
                'description' => $this->form->description,
                'version' => $version,
            ]);
            $termsCondition->roles()->sync($this->form->roles);
            DB::commit();

            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Contract Updated Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }


    public function getVersion($termsCondition_id)
    {
        $termsCondition = TermsCondition::findOrFail($termsCondition_id);
        return $termsCondition->version += 0.1;
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
            $userTermsCondition = TermConditionUser::findOrFail($id);
            $userTermsCondition->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'User Terms&Condition Deleted Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }
}
