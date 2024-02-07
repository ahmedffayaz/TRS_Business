<?php

namespace App\Livewire\Backend;

use App\Livewire\Forms\PermissionForm;
use App\Traits\WithMainModal;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Spatie\Permission\Models\Permission;

class PermissionComponent extends Component
{
    use WithPagination;
    use WithMainModal;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public int $limitPerPage = 10;
    public PermissionForm $form;

    private function getPermissions(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        // getList($this->search, $this->columnName, $this->sortDirection)
        return Permission::paginate($this->limitPerPage);
    }

    #[Title('User Permissions')]
    public function render()
    {
        $permissions = $this->getPermissions();
        return view('livewire.backend.permission-component', compact('permissions'));
    }

    public function store()
    {
        $this->form->validate();
        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existingCount = DB::table('permissions')
                        ->where('title', $value)
                        ->count();

                    if ($existingCount > 0) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ]
        ]);
        try {
            DB::beginTransaction();
            Permission::create([
                'group' => $this->form->group,
                'title' => $this->form->title,
                'name' => Str::slug($this->form->title, '-')
            ]);
            DB::commit();
            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Permission Created Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function edit($id)
    {
        $this->form->isUpdate = true;
        $this->form->id = $id;
        try {
            $permission = Permission::findOrFail($id);
            $this->form->set($permission);
            $this->openMainModal();
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }

    public function update($id)
    {
        $this->form->validate();
        $permission = Permission::findOrFail($id);

        $this->form->validate([
            'title' => [
                'required',
                function ($attribute, $value, $fail) use ($permission) {
                    $existingCount = DB::table('permissions')
                        ->where('title', $value)
                        ->where('id', '!=', $permission->id)
                        ->count();

                    if ($existingCount > 0) {
                        $fail("The $attribute has already been taken.");
                    }
                }
            ]
        ]);
        try {

            DB::beginTransaction();
            $permission->group = $this->form->group;
            $permission->title = $this->form->title;
            $permission->name = Str::slug($this->form->title, '-');
            $permission->save();
            DB::commit();

            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Permission Updated Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
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
            $permission = Permission::findOrFail($id);
            $permission->delete();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Permission Deleted Successfully!']);
        } catch (\Exception $exception) {
            $this->dispatch('alert', ['type' => 'error',  'message' => $exception->getMessage()]);
        }
    }
}
