<?php

namespace App\Livewire\Backend\Leaves;

use App\Enums\Leave\LeaveStatus;
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
    public $isShowReason = false;

    public $cancelReason = false;
    public $rejectReason = false;
    public $reason = "";

    public $cancel_id = null;

    public $rejection_id = null;

    public $pendingStatus = LeaveStatus::PENDING;
    public $approvedStatus = LeaveStatus::APPROVED;
    public $canceledStatus = LeaveStatus::CANCELED;
    public $rejectedStatus = LeaveStatus::REJECTED;

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
                'business_id' => $this->business->id,
            ]);

            DB::commit();
            $this->form->reset();
            $this->resetValidation();
            $this->closeMainModal();
            $this->dispatch('reinitialize-dispatcher');
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
        $leaves =  Leave::sessionBusiness()
            ->when(!auth()->user()->hasRole(['super-admin', 'admin', 'project-manager']), function ($query) {
                $query->whereUserId(auth()->user()->id);
            })->orderBy('id', $this->sortDirection)
            ->getList($this->search, $this->columnName, $this->sortDirection)
            ->paginate($this->limitPerPage);
            $this->dispatch('reinitialize-dispatcher');
            return $leaves;
    }
    public function viewReason($id)
    {
        $this->isShowReason = true;
        $leave = Leave::select('reason')->find($id);
        $this->reason = $leave->reason;
        $this->openMainModal();
    }
    public function openModal()
    {
        $this->rejectReason = false;
        $this->isShowReason = false;
        $this->cancelReason = false;
        $this->openMainModal();
    }

    public function closeModal()
    {
        $this->rejectReason = false;
        $this->cancelReason = false;
        $this->isShowReason = false;
        $this->closeMainModal();
    }
    public function edit($id)
    {
        $this->rejectReason = false;
        $this->cancelReason = false;
        $this->isShowReason = false;
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
        $this->rejectReason = false;
        $validated = $this->form->validate();

        try {
            $leave = Leave::findOrFail($id);

            DB::beginTransaction();
            $leave->update([
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
            $this->dispatch('reinitialize-dispatcher');
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
    #[On('delete')]
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Leave::findOrFail($id)->delete();
            DB::commit();
            $this->dispatch('reinitialize-dispatcher');
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
            'type' => 'Cancel-reason',
            'iconType' => 'warning',
            'title' => 'Cancel leave?',
            'description' => 'Are you sure to cancel the leave.',
        ]);
    }
    #[on('Cancel-reason')]
    public function cancelReason($id)
    {
        $this->reason = "";
        $this->cancelReason = true;
        $this->openMainModal();
        $this->cancel_id = $id;
        $this->rejectReason = false;
        $this->isShowReason = false;
    }

    protected $rules = [
        'reason' => 'required|min:10',
    ];
    public function cancelLeave()
    {
        $this->rejectReason = false;
        $this->cancelReason = false;
        $this->isShowReason = false;
        $id = $this->cancel_id;
        $validatedData = $this->validate($this->rules);
        try {
            DB::beginTransaction();
            Leave::where('id', $this->cancel_id)->update([
                'status' => 'canceled',
                'processing_reason' => $validatedData['reason']
            ]);
            DB::commit();
            $this->dispatch('reinitialize-dispatcher');
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Leave Canceled successfully.'
            ]);
            $this->closeModal();
            $this->cancelReason = false;
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
    public function ApproveConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'approve',
            'iconType' => 'warning',
            'title' => 'Approve leave?',
            'description' => 'Are you sure to Approve the leave.',
        ]);
    }
    #[on('approve')]
    public function ApproveLeave($id)
    {
        try {
            $leave_by_business = Leave::sessionBusiness();

            if (auth()->user()->hasRole(['super-admin', 'admin', 'project-manager'])) {
                $leave = $leave_by_business->findOrFail($id);
                $leave->update([
                    'status' => 'approved',
                    'processed_by' => auth()->user()->id,

                ]);
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave Approved.']);
            } else {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'You have no access to approve the leaves. ']);
            }
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while add time: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    public function RejectConfiramtion($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'Reject-reason',
            'iconType' => 'warning',
            'title' => 'Reject leave?',
            'description' => 'Are you sure to Reject the leave.',
        ]);
    }
    #[on('Reject-reason')]
    public function rejectionReason($id)
    {
        $this->reason = "";
        $this->rejectReason = true;
        $this->openMainModal();
        $this->rejection_id = $id;
        $this->isShowReason = false;
        $this->cancelReason = false;
    }
    public function rejectLeave()
    {
        $this->rejectReason = false;
        $this->cancelReason = false;
        $this->isShowReason = false;
        $id = $this->rejection_id;
        $validatedData = $this->validate($this->rules);
        try {
            $leave_by_business = Leave::sessionBusiness();

            if (auth()->user()->hasRole(['super-admin', 'admin', 'project-manager'])) {
                $leave = $leave_by_business->findOrFail($id);
                $leave->update([
                    'status' => 'rejected',
                    'processed_by' => auth()->user()->id,
                    'processing_reason' => $validatedData['reason']

                ]);
                $this->dispatch('reinitialize-dispatcher');
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave Rejected Successfully.']);
                $this->closeModal();
                $this->reason = "";
                $this->rejectReason = false;
            } else {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'You have no access to approve the leaves. ']);
            }
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is rejecting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while Leave is rejecting and Leave id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    public function render()
    {
        $leaves = $this->getLeaves();
        $showCancel = $leaves->where('status'=='pending')->where('user_id', auth()->user()->id);
        return view('livewire.backend.leaves.leaves-component', compact('leaves', 'showCancel'));
    }
}
