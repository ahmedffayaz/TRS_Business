<?php

namespace App\Livewire\Backend\Invoice;

use App\Enums\Invoice\InvoiceStatus;
use App\Jobs\SendCreateProjectInvoice;
use App\Models\Task;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Business;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateInvoiceComponent extends Component
{

    public $tasks = [];
    public $selectedTasks = [];

    public $i;
    public $inputs = [];
    public array $clientName = [];
    public InvoiceForm $invoiceForm;
    public $project;
    public $business;

    public $invoice_number;

    public function mount(Request $request)
    {
        if ($request->has('tasks')) {
            $taskIds = explode(',', $request->query('tasks'));

            $this->tasks = Task::with(['project.client.country', 'comments'])
                ->whereIn('id', $taskIds)
                ->whereHas('comments', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->get();
            foreach ($this->tasks as $task) {
                $this->invoiceForm->task[$task->id]['task_amount'] = $task->project?->hourly_rate;
                $this->clientName[] = $task->project->client->name;
                $this->clientName[] = $task->project->client->address;
                $this->invoiceForm->project_id = $task->project?->id;
                $this->invoiceForm->currency = $task->project?->currency;
                $this->business = $task->project?->client?->business;
                $this->project = $task->project;
            }
            $this->invoiceForm->due_at = Carbon::now()->addDays(7)->format('Y-m-d');
            $this->selectedTasks = $this->tasks;
        }
        $this->invoice_number = $this->generateUniqueInvoiceNumber();
        $this->inputs = [];
        $this->i = 0;
    }
    public function render()
    {
        $this->dispatch('reinitialize-dispatcher');
        $business = Business::findOrFail(session('business_details.id'));
        // dd($this->selectedTasks->first()->project);
        return view('livewire.backend.invoice.create-invoice-component')->with([
            'tasks' => $this->selectedTasks,
            'business' => $business
        ]);
    }
    private function generateUniqueInvoiceNumber()
    {
        $invoiceNumber = $this->business?->invoice_prefix . $this->business?->invoice_serial;
        $serialLength = strlen($this->business?->invoice_serial);
        $serial = (int) $this->business?->invoice_serial + 1;
        $newSerial = str_pad($serial, $serialLength, '0', STR_PAD_LEFT);
        $this->business->update([
            'invoice_serial' => $newSerial,
        ]);
        return $invoiceNumber;
    }
    public function store()
    {
        $validated = $this->invoiceForm->validate();
        try {
            DB::beginTransaction();
            $invoice = Invoice::create([
                'project_id' => $validated['project_id'],
                'invoice_number' => $this->invoice_number,
                // 'file'=>$validated['file'],
                'currency' => $validated['currency'],
                'deduction' => $validated['deduction'],
                'total' => $validated['total_amount'],
                'notes' => $validated['description'],
                'due_at' => $validated['due_at'],
                'billed_at' => null,
                'status' => InvoiceStatus::PROCESSING,

            ]);
            $projectCost = 0;
            if (isset($validated['task'])) {
                foreach ($validated['task'] as $key => $task) {
                    $time = 0;
                    $time = $task['time'];
                    $comments = [];
                    foreach ($task['comments'] as $commentId => $value) {
                        $time = floatval($time);
                        $comment = Comment::findOrFail($commentId);
                        $comment->update(['invoiced_at' => now()]);
                        $comments[] = $comment->id;
                    }
                    $ratePerHour = null;
                    $totalCost = 0;
                    if ($this?->project?->type->value === 'hourly') {
                        $totalCost = $this->project->hourly_rate * ($time / 60);
                        $ratePerHour = $this->project->hourly_rate;
                    } else if ($this?->project?->type?->value === 'fixed') {
                        $totalCost = $task['task_amount'];
                    }

                    $projectCost += $totalCost;
                    $invoice->invoiceData()->create([
                        'task_id' => $key,
                        'time' => $time,
                        'rate_per_hour' => $ratePerHour,
                        'amount' => $totalCost,
                        'comments' => implode(',', $comments)
                    ]);
                }
            }
            // generate Invoice
            $invoice->update(['status' => InvoiceStatus::PROCESSING->value]);
            $invoice = $invoice->with(['invoiceData.task'])->find($invoice->id);
            foreach ($invoice->invoiceData as $key => $record) {
                $comments = Comment::whereIn('id', explode(',', $record->comments))->get();
                if ($record->task) {
                    $record->task->setRelation('comments', $comments);
                }
            }
            if (isset($validated['generic_comments'])) {
                foreach($validated['generic_comments'] as $comment) {
                    $invoice->invoiceData()->create([
                        'time' => $comment['quantity'] ? $comment['quantity'] * 60 : 0,
                        'rate_per_hour' => $comment['rate'],
                        'amount' => $comment['amount'],
                        'comments' => $comment['description']
                    ]);
                }
            }

            $deduction = ($projectCost === 0) ? 0 : $validated['deduction'];
            $invoice->update([
                'total' => $projectCost + ($projectCost === 0 && $validated['deduction'] > 0 ? $validated['deduction'] : 0),
                'deduction' => $deduction,
            ]);
            $this->generateInvoice($invoice);
            DB::commit();
            $this->invoiceForm->total_amount = null;
            $this->invoiceForm->due_at = null;
            $this->invoiceForm->currency = null;
            $this->invoice_number = null;
            $this->invoiceForm->task = null;
            $this->invoiceForm->description = null;
            session()->flash('success', 'Invoice created successfully.');
            return redirect()->route('dashboard.invoices.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create invoice: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => $exception->getMessage()]);
        }
    }
    private function generateInvoice($data, $isEmails = true)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOption(['isPhpEnable' => true])->setPaper('a4', 'portrait');
        $fileName = $data->invoice_number . '.pdf';

        if (!Storage::disk('public')->exists(getStoragePath('invoice'))) {
            Storage::disk('public')->makeDirectory(getStoragePath('invoice'));
        }

        $invoicePdfFile = public_path('storage/' . getStoragePath('invoice')) . '/' . $fileName;
        $view = 'livewire.backend.invoice.invoice-pdf';
        $pdf->loadView($view, compact('data'))->save($invoicePdfFile);

        $invoice = $data;
        $data->update([
            'status' => 'processed',
            'file' => 'storage/' . getStoragePath('invoice') . '/' . $fileName,
        ]);

        if ($data->send_emails && $isEmails) {
            $invoiceNumber = $data->invoice_number;
            $user = User::where('client_id', $data->project->client_id)->first();
            if ($user) {
                $uEmail = $user->email;

                // get super and and admin users
                $users = User::whereHas('roles', function ($query) {
                    $query->where('name', 'super-admin')
                        ->orWhere('name', 'admin');
                })->get();

                $userEmail = array();
                foreach ($users as $user) {
                    $temp = $user->email;
                    array_push($userEmail, $temp);
                }
                array_push($userEmail, $uEmail);

                $emailData = [
                    'first_name' => $user->first_name,
                    'invoice_number' => $invoiceNumber,
                    'business_name' => $invoice?->project?->business?->name,
                    'business_logo' => $invoice?->project?->business?->logo
                ];

                $filteredKeywords = ['{{CLIENT_NAME}}', '{{PROJECT}}', '{{INVOICE_NUMBER}}'];
                $filteredKeywordsValue = [$emailData['first_name'], $invoice?->project?->name, $emailData['invoice_number']];

                // Dispatch email to admin
                dispatch(new SendCreateProjectInvoice($invoice, $fileName, $data, $userEmail, $filteredKeywords, $filteredKeywordsValue));
            }
        }
    }
    public function addGenericCommentsFields($i)
    {
        $this->i = $i + 1;
        array_push($this->inputs, $this->i);
        $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-feather-icons');
    }
    public function removeGenericCommentsFields($key)
    {
        unset($this->inputs[$key]);
        $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-feather-icons');
    }
}
