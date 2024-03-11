<?php

namespace App\Livewire\Backend\TermsCondition;

use Exception;
use App\Models\Role;
use Livewire\Component;
use App\Models\Business;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Models\TermsCondition;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Livewire\Forms\TermsConditionForm;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Title('Terms & Conditions')]
class TermsConditionComponent extends Component
{
    use WithMainModal, WithPagination;

    public $business_id;
    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 15;

    public TermsConditionForm $form;

    public function mount()
    {
        $this->business_id = Business::whereName(session('business'))->first()->id;
    }

    private function getTermsConditions() : LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        $query = TermsCondition::sessionBusiness();
        $data = auth()->user()->hasRole('super-admin')
            ? $query
            : $query->whereHas('roles', function ($query) {
                $query->whereIn('name', auth()->user()->roles->pluck('name')->toArray());
            })->orWhere(function ($query) {
                $query->whereDoesntHave('roles');
            });

        $paginatedData = $data->with(['roles', 'business'])->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
        return $paginatedData;
    }

    public function render()
    {
        $roles = Role::all();
        $termsConditions = $this->getTermsConditions();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.terms-condition.terms-condition-component', compact('roles', 'termsConditions'));
    }

    public function closeModal()
    {
        $this->dispatch('close-main-modal');
        $this->form->reset();
        $this->resetValidation();
        $this->dispatch('roles-select', ['formRoles' => []]);
    }

    public function store()
    {
        $this->form->business_id = $this->business_id;
        $this->form->created_by = auth()->user()->id;
        $this->form->updated_by = auth()->user()->id;

        $validated = $this->form->validate();

        try {
            DB::beginTransaction();

            $termsCondition = TermsCondition::create([
                'business_id' => $validated['business_id'],
                'uuid' => getUuid(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'version' => $validated['version'],
                'created_by' => $validated['created_by'],
                'updated_by' => $validated['updated_by']
            ]);

            $termsCondition->roles()->attach($validated['roles']);

            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Terms & condition added successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while save new terms and conditions: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        try {
            $this->form->isUpdate = true;
            $termsCondition = TermsCondition::sessionBusiness()->whereId($id)->with('roles')->firstOrFail();
            $this->form->set($termsCondition);

            // Set the selected roles in the form
            $this->form->roles = $termsCondition->roles->pluck('id')->toArray();
            $this->dispatch('roles-select', ['formRoles' => $this->form->roles]);

            $this->openMainModal();
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error while open edit model of terms and conditions: ' . $exception);
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            Log::error('Get error while open edit model of terms and conditions: ' . $exception);
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $this->form->isUpdate = true;
        $this->form->updated_by = auth()->user()->id;
        $this->form->business_id = $this->business_id;

        $validated = $this->form->validate();

        try {
            $termsCondition = TermsCondition::sessionBusiness()->whereId($id)->firstOrFail();

            $termsCondition->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'version' => $validated['version'],
                'updated_by' => $validated['updated_by']
            ]);

            $termsCondition->roles()->sync($validated['roles']);

            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Terms & condition updated successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update terms and conditions: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error',  'message' => 'Something went wrong.']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'delete',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to delete the terms & conditions. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $termsCondition = TermsCondition::sessionBusiness()->findOrFail($id);
            $termsCondition->delete();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Terms & conditions deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            Log::error('Get error while delete terms & conditions: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the terms & conditions could not be found in our database.']);
        } catch (Exception $exception) {
            Log::error('Get error while delete terms & conditions: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Something went wrong.']);
        }
    }
}
