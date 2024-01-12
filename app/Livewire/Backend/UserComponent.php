<?php

namespace App\Livewire\Backend;

use App\Models\User;
use Livewire\Component;
use App\Models\Currency;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithOffcanvas;
use App\Enums\User\UserStatus;
use App\Livewire\Forms\UserForm;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserComponent extends Component
{
    use WithPagination;
    use WithOffcanvas;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public UserForm $form;

    private function getUsers(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        return User::getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
    }

    public function render()
    {
        $currencies = Currency::get(['code']);
        $userStatuses = UserStatus::cases();
        $companies = Company::all();
        $users = $this->getUsers();
        return view('livewire.backend.user-component', compact('currencies', 'userStatuses', 'companies', 'users'));
    }

    public function store()
    {
        $validated = $this->form->validate();
        try {
            $user = array_merge($validated, ['email_verified_at' => now()]);
            User::create($user);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'User Created Successfully!']);
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $user = User::findOrFail($id);
            $this->form->set($user);
            $this->openOffcanvas();
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the user could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();
        if ($validated['password'] === null) {
            unset($validated['password'], $validated['password_confirmation']);
        }
        try {
            User::findOrFail($id)->update($validated);
            $this->closeOffcanvas();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'User Updated Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the user could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = !$user->is_active;
            $user->update();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Status Changed Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the user could not be found in our database.');
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
            'description' => 'You are about to delete the employee. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'User Deleted Successfully!']);
        } catch (ModelNotFoundException $exception) {
            session()->flash('error', 'Sorry, the user could not be found in our database.');
        } catch (\Exception $exception) {
            session()->flash('error', $exception->getMessage());
        }
    }
}
