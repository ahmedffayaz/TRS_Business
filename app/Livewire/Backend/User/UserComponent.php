<?php

namespace App\Livewire\Backend\User;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Livewire\Component;
use App\Models\Business;
use App\Models\Currency;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Enums\User\UserStatus;
use Livewire\Attributes\Title;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[Title('Users')]
class UserComponent extends Component
{
    use WithPagination, WithMainModal;

    public $business_id;
    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public string $dataCountType = 'total'; // Default user type
    public UserForm $form;
    public $roleId = '';
    public $filterStatus = '';
    public $joinedFrom = '';


    public function mount()
    {
        $this->business_id = Business::whereName(session('business'))->first()->id;
        // Load total users by default when the component is mounted
        $this->getTotalUsers();
    }

    public function getUserQuery()
    {
        $user = auth()->user();
        $this->search ? $this->resetPage() : ''; // reset pagination while searching

        return User::sessionBusiness()
            ->when($user->hasRole('client'), function ($query) use ($user) {
                // For clients: Show only their own data
                $query->whereHas('roles', function ($query) {
                    $query->where('name', 'client');
                })
                    ->where('client_id', $user->client_id)
                    ->where('is_active', 1);
            }, function ($query) {
                // For admins and others: Show all users except those with the 'client' role
                $query->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'client');
                });
            })
            ->when($this->roleId, function ($query) {
                $query->whereHas('roles', function ($query) {
                    $query->where('id', $this->roleId);
                });
            })
            ->when($this->filterStatus, function ($query) {
                // Filter by active status
                $isActive = $this->filterStatus === 'active' ? 1 : 0;
                $query->where('is_active', $isActive);
            })
            ->when($this->joinedFrom, function ($query) {
                // Filter by join date
                $dateRange = $this->joinedFrom;
                if (strpos($dateRange, ' to ') !== false) {
                    [$joinedFrom, $joinedTo] = explode(' to ', $dateRange);
                    $joinedFrom = Carbon::createFromFormat('Y-m-d', $joinedFrom)->startOfDay();
                    $joinedTo = Carbon::createFromFormat('Y-m-d', $joinedTo)->endOfDay();
                    $query->whereBetween('created_at', [$joinedFrom, $joinedTo]);
                } else {
                    $query->whereDate('created_at', '>=', $dateRange);
                }
            })
            ->getList($this->search, $this->columnName, $this->sortDirection);
    }


    private function getTotalUsers(): LengthAwarePaginator
    {
        return $this->getUserQuery()->paginate($this->limitPerPage);
    }

    private function getActiveUsers(): LengthAwarePaginator
    {
        return $this->getUserQuery()->where('is_active', 1)->paginate($this->limitPerPage);
    }

    private function getArchivedUsers(): LengthAwarePaginator
    {
        return $this->getUserQuery()->where('is_active', 0)->paginate($this->limitPerPage);
    }

    public function getUsers()
    {
        return $this->getUserQuery()->paginate($this->limitPerPage);
    }

    public function render()
    {
        $currencies = Currency::get(['code']);
        $userStatuses = UserStatus::cases();
        $clients = Client::sessionBusiness()->get();
        $roles = Role::where('name', '!=', 'client')->where('business_id', $this->business_id)->get();
        $users = $this->getUsers();
        $totalUsers = User::sessionBusiness()->where(function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client')->where('business_id', $this->business_id);
            });
        })->count();

        $activeUsers = User::sessionBusiness()->where(function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client')->where('business_id', $this->business_id);
            });
        })->where('is_active', 1)->count();

        $archivedUsers = User::sessionBusiness()->where(function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', '!=', 'client')->where('business_id', $this->business_id);
            });
        })->where('is_active', 0)->count();

        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.user.user-component', compact('currencies', 'userStatuses', 'clients', 'roles', 'users', 'totalUsers', 'activeUsers', 'archivedUsers'));
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
                'business_id' => $this->business_id,
                'designation' => $validated['designation'],
                'phone' => $validated['phone'],
                'alternative_number' => $validated['alternative_number'],
                'address' => $validated['address'],
                'salary' => $validated['salary'],
                'currency' => $validated['currency'],
                'is_active' => $validated['is_active'],
            ]);

            DB::table('model_has_roles')->insert([
                'role_id' => $this->form->roles[0],
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);
            DB::commit();
            $this->closeModal();

            $this->dispatch('alert', ['type' => 'success', 'message' => 'User created successfully.']);
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
                'business_id' => $this->business_id,
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
                'message' => 'Sorry, the user could not be found in our database.'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function deactivateUserConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'deactivate',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You want to deactivate this user, user can not be login after this.',
        ]);
    }

    #[On('deactivate')]
    public function deactivateUser($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update(['is_active' => false]);
            DB::commit();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User deactivated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error on deactivate user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on deactivate user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function activateUserConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'activate',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You want to activate this user.',
        ]);
    }

    #[On('activate')]
    public function activateUser($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->update(['is_active' => true]);
            DB::commit();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', ['type' => 'success', 'message' => 'User activated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error on deactivate user: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on deactivate user: ' . $exception->getMessage());
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
            'description' => 'You are about to delete the user. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
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
    public function applyFilter($roleId, $status, $joinedFrom)
    {
        $this->roleId = $roleId;
        $this->filterStatus = $status;
        $this->joinedFrom = $joinedFrom;
    }
    #[On('reset-user-filter')]
    public function resetFilters()
    {
        $this->dispatch('reset-filters');
        $this->reset(['roleId', 'filterStatus', 'joinedFrom', 'search']);
    }
}
