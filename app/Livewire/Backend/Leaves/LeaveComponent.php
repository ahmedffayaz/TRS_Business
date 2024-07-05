<?php

namespace App\Livewire\Backend\Leaves;

use App\Traits\WithMainModal;
use Livewire\Component;
use App\Models\Leave;
use App\Livewire\Forms\LeaveForm;
use App\Models\Business;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Title('Leaves')]
class LeaveComponent extends Component
{
    use WithMainModal, WithPagination;

    public LeaveForm $form;
    public string $sortDirection = 'desc';
    public string $search = '';
    public string $columnName = 'created_at';
    public int $limitPerPage = 10;
    public $business;

    public function mount()
    {
        $this->business = Business::whereName(session('business'))->first();
    }

    public function createLeave()
    {
        $validated = $this->form->validate();
        try {
            DB::beginTransaction();
            Leave::create([
                'reason' => $validated['reason'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'user_id' => auth()->user()->id,
                'is_working' => $validated['is_working'],
            ]);

            DB::commit();
            $this->form->reset();
            $this->resetValidation();
            $this->closeMainModal();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave created successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    private function getLeaves(): LengthAwarePaginator
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching
        $user = auth()->user();
        $leavesQuery = Leave::orderBy('id', $this->sortDirection)
        ->getList($this->search, $this->columnName, $this->sortDirection);

        if (!$user->hasRole(['super-admin', 'admin', 'project-manager'])) {
            $leavesQuery->whereHas('user', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }

        $leaves = $leavesQuery->paginate($this->limitPerPage);

        return $leaves;
    }
    public function viewReason($id)
    {
        $leaveReason =   Leave::where('id',$id)->select('reason')->first();
        $this->openModal();
    }
    public function openModal()
    {
        $this->openMainModal();
    }

    public function closeModal()
    {
        $this->closeMainModal();
    }
    public function edit($id)
    {
        $this->form->isUpdate = true;
        try {
            $leave = Leave::findOrFail($id);
            $this->form->set($leave);

            $this->openModal();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while edit Leave and Leave id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit Leave and Leave id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            $comment = Leave::findOrFail($id);

            DB::beginTransaction();
            $comment->update([
                'reason' => $validated['reason'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'user_id' => auth()->user()->id,
                'is_working' => $validated['is_working'],
            ]);

            DB::commit();
            $this->closeModal();
            $this->form->reset();
            $this->resetValidation();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave updated successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update Leave and Leave id is: ' . $id . ' and error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update Leave and Leave id is: ' . $id . ' and error: ' . $exception->getMessage());
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
            'description' => 'You are about to cancel the leave.',
        ]);
    }
    #[On('delete')]
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Leave::findOrFail($id)->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Leave deleted successfully.'
            ]);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is deleting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is deleting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function CancelConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'Cancel',
            'iconType' => 'warning',
            'title' => 'Cancel leave?',
            'description' => 'Are you sure to cancel the leave.',
        ]);
    }
    #[On('Cancel')]
    public function cancelLeave($id)
    {
        try {
            DB::beginTransaction();
            Leave::where('id', $id)->update(['status' => 'canceled']);
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Leave Canceled successfully.'
            ]);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is deleting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is deleting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    public function render()
    {
        $leaves = $this->getLeaves();
        return view('livewire.backend.leaves.leaves-component', compact('leaves'));
    }
}
