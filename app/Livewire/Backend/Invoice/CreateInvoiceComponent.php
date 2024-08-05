<?php

namespace App\Livewire\Backend\Invoice;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Business;
use Illuminate\Support\Facades\Log;


class CreateInvoiceComponent extends Component
{

    public $tasks = [];
    public $selectedTasks = [];
    public InvoiceForm $invoiceForm;

    public function mount(Request $request)
    {
        if ($request->has('tasks')) {
            $taskIds = explode(',', $request->query('tasks'));
            $this->tasks = Task::with(['project.client.country'])->whereIn('id', $taskIds)->get();
            $this->selectedTasks = $this->tasks;
        }
    }
    public function render()
    {
        $business = Business::findOrFail(session('business_details.id'));
        // dd($this->selectedTasks->first()->project);
        return view('livewire.backend.invoice.create-invoice-component')->with([
            'tasks' => $this->selectedTasks, 
            'business' => $business
        ]);
    }

    public function store() {
        try {
            DB::beginTransaction();
            dd('data coming');
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create invoice: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
