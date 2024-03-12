<?php

namespace App\Livewire\Backend\User;

use Exception;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Livewire\Component;
use App\Models\Currency;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Enums\User\UserStatus;
use Livewire\Attributes\Title;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

#[Title('Users')]
class UserComponent extends Component
{
    use WithPagination, WithMainModal;

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
        $clients = Client::sessionBusiness()->get();
        $roles = Role::all();
        $users = $this->getUsers();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.user.user-component', compact('currencies', 'userStatuses', 'clients', 'roles', 'users'));
    }

    public function closeModal()
    {
        $this->dispatch('close-main-modal');
        $this->form->reset();
        $this->resetValidation();
        $this->dispatch('select-client', ['formClient' => []]);
        $this->dispatch('select-currency', ['formCurrency' => []]);
        $this->dispatch('select-status', ['formStatus' => []]);
        $this->dispatch('select-roles', ['formRoles' => []]);
    }

    public function store()
    {
        $validated = $this->form->validate();

        try {
            DB::beginTransaction();
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'alternative_email' => $validated['alternative_email'],
                'email_verified_at' => now(),
                'password' => $validated['password'],
                'client_id' => $validated['client_id'],
                'designation' => $validated['designation'],
                'phone' => $validated['phone'],
                'alternative_number' => $validated['alternative_number'],
                'address' => $validated['address'],
                'salary' => $validated['salary'],
                'currency' => $validated['currency'],
                'is_active' => $validated['is_active'],
            ]);

            $user->roles()->attach($validated['roles']);

            DB::commit();

            $this->closeModal();

            $this->dispatch('alert', ['type' => 'success', 'message' => 'Sorry, the user could not be found in our database.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $user = User::with(['roles'])->findOrFail($id);
            $this->form->set($user);

            $this->dispatch('select-client', ['formClient' => $this->form->client_id]);
            $this->dispatch('select-currency', ['formCurrency' => $this->form->currency]);
            $this->dispatch('select-status', ['formStatus' => $this->form->is_active]);

            // Set the selected roles in the form
            $this->form->roles = $user->roles->pluck('id')->toArray();
            $this->dispatch('select-roles', ['formRoles' => $this->form->roles]);

            $this->openMainModal();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while edit user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Sorry, the user could not be found in our database.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            $user = User::findOrFail($id);

            DB::beginTransaction();

            $user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'alternative_email' => $validated['alternative_email'],
                'email_verified_at' => now(),
                'password' => $validated['password'] !== null ? $validated['password'] : $user->password,
                'client_id' => $validated['client_id'],
                'designation' => $validated['designation'],
                'phone' => $validated['phone'],
                'alternative_number' => $validated['alternative_number'],
                'address' => $validated['address'],
                'salary' => $validated['salary'],
                'currency' => $validated['currency'],
                'is_active' => $validated['is_active'],
            ]);

            $user->roles()->sync($validated['roles']);

            DB::commit();

            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User updated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update user: ' . $exception->getMessage());
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Sorry, the user could not be found in our database.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = !$user->is_active;
            $user->update();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Status changed successfully!']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while changing user status: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Sorry, the user could not be found in our database.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while changing user status: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
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
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while deleting user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Sorry, the user could not be found in our database.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while deleting user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
}
