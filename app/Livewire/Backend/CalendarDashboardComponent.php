<?php

namespace App\Livewire\Backend;

use App\Models\Leave;
use Livewire\Component;
use App\Traits\WithMainModal;
use Livewire\Attributes\On;

class CalendarDashboardComponent extends Component
{
    use WithMainModal;
    public $form = [
        'start_date' => '',
        'end_date' => '',
        'is_working' => '',
        'reason' => '',
        'id' => '',
    ];

    protected $rules = [
        'form.start_date' => 'required|date',
        'form.end_date' => 'required|date|after_or_equal:form.start_date',
        'form.is_working' => 'required|in:0,1,2',
        'form.reason' => 'nullable|string|max:255',
    ];


    public function submitLeaveRequest()
    {

        try {

            if(!auth()->user()->hasPermissionTo('add_leaves')){
                $this->dispatch('alert', ['type' => 'error', 'message' => 'You have no permission to add leave.']);
                return;
            }

            $this->validate();

            Leave::updateOrCreate(['id' => $this->form['id']], [
                $this->form['id'] ?  : 'user_id' => auth()->id(),
                // 'user_id' => auth()->id(),
                'start_date' => $this->form['start_date'],
                'end_date' => $this->form['end_date'],
                'is_working' => $this->form['is_working'],
                'reason' => $this->form['reason'],
                'business_id' => auth()->user()->business_id,
            ]);

            $this->dispatch('close-main-modal');
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave request submitted successfully.']);

        } catch (\Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Failed to submit leave request']);
        }
    }


    public function closeLeaveModal()
    {
        $this->dispatch('reinitialize-calendar');
        $this->reset('form');
        $this->dispatch('close-main-modal');
    }

    #[On('destroy-leave', ['id'])]
    public function destroyLeave($id)
    {
        try {
            $leave = Leave::findOrFail($id);
            $leave->delete();

            $this->dispatch('alert', ['type' => 'success', 'message' => 'Leave record deleted successfully.']);
        } catch (\Exception $e) {

            $this->dispatch('alert', ['type' => 'error', 'message' => 'Failed to delete leave record: ']);
        }
    }


    public function render()
    {
        $this->dispatch('reinitialize-calendar');
        return view('livewire.backend.calender-dashboard-component');
    }
}
