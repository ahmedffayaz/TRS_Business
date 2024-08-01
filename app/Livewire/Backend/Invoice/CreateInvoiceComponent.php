<?php

namespace App\Livewire\Backend\Invoice;

use App\Models\Task;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\InvoiceForm;
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
            $this->tasks = Task::whereIn('id', $taskIds)->get();
            $this->selectedTasks = $taskIds;
        }
    }
    public function render()
    {
        dd(session('business'));
        return view('livewire.backend.invoice.create-invoice-component')->with([
            'tasks' => $this->selectedTasks
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
