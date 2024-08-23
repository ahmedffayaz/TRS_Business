<?php

namespace App\Livewire\Backend\Invoice;

use App\Enums\Invoice\InvoiceStatus;
use App\Jobs\SendCreateProjectInvoice;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Business;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Illuminate\Http\Request;

class EditInvoiceComponent extends Component
{
    public InvoiceForm $invoiceForm;
    public array $client = [];
    public $DraftInvoiceNumber;

    public $id;
    public $i;
    public $inputs = [];
    public $project;

    public $totalGenericAmount;
    public $taskVisibility = [];
    public function mount($id)
    {
        $this->id = $id;
        $invoiceDraft = Invoice::where('id', $id)->first();
        $this->invoiceForm->due_at = $invoiceDraft?->due_at;
        $this->invoiceForm->deduction = $invoiceDraft?->deduction;
        $this->invoiceForm->total_amount = $invoiceDraft?->total;
        $this->invoiceForm->currency = $invoiceDraft?->currency;
        $this->invoiceForm->description = $invoiceDraft?->notes;
        $this->invoiceForm->project_id = $invoiceDraft?->project_id;
        $this->DraftInvoiceNumber = $invoiceDraft->invoice_number;

        $invoiceDataCollection = $invoiceDraft->invoiceData->where('invoice_id', $invoiceDraft->id);

        $invoiceData = $invoiceDataCollection->first();
        $taskId = $invoiceData->task_id;
        $this->invoiceForm->id = $id;
        $this->invoiceForm->isUpdate = true;

        $invoice = Invoice::with(['invoiceData.task', 'project'])
            ->whereHas('invoiceData')
            ->where('id', $id)
            ->first();
        $draftInvoiceNumber = $invoice->invoice_number;
        $draftDueAt = $invoice->due_at;
        foreach ($invoice->invoiceData->whereNotNull('task_id') as $invoiceData) {
            // Access the task related to each invoiceData
            $task = $invoiceData->task;
            $this->client[] = $task?->project?->client?->name;
            $this->client[] = $task?->project?->client?->address;
            $this->invoiceForm->task[$task?->id]['task_amount'] = $invoiceData?->rate_per_hour;
            $this->invoiceForm->task[$task?->id]['unit'] =  $this->invoiceForm->currency;
            $this->invoiceForm->task[$task?->id]['time'] = $invoiceData->time;
            $this->invoiceForm->task[$task?->id]['amount'] = $invoiceData->amount;
        }

        $index = 0;
        foreach ($invoice->invoiceData->whereNull('task_id') as $invoiceData) {
            $this->invoiceForm->generic_comments[$index]['description'] = $invoiceData?->comments;
            $this->invoiceForm->generic_comments[$index]['quantity'] = $invoiceData?->qty;
            $this->invoiceForm->generic_comments[$index]['amount'] = $invoiceData?->amount;
            $this->invoiceForm->generic_comments[$index]['rate'] = $invoiceData?->rate_per_hour;
            array_push($this->inputs, $index);
            $this->totalGenericAmount += $invoiceData?->amount;
            $index++; // Increment the index
        }
        $this->i = 0;
        $this->dispatch('reinitialize-feather-icons');
        $this->dispatch('reinitialize-dispatcher');
    }
    public function render()
    {
        $this->dispatch('reinitialize-feather-icons');
        $this->dispatch('reinitialize-dispatcher');
        $invoice = Invoice::where('id', $this->id)->with('invoiceData')->first();
        $business = Business::findOrFail(session('business_details.id'));
        $tasks = Task::whereHas('invoiceData', function ($query) {
            $query->whereHas('invoice', function ($query) {
                $query->where('id', $this->id);
            });
        })->with(['comments', 'project'])->get();
        $this->project = $invoice->project;
        $invoiceDataTaskCommentIds = [];
        foreach ($invoice->invoiceData as $data) {
            if (!empty($data->task_id)) {
                $comments = explode(',', $data->comments);
                $comments = array_map('trim', $comments);
                foreach ($comments as $comment) {
                    $invoiceDataTaskCommentIds[] = $comment;
                }
            }
        }
        return view('livewire.backend.invoice.edit-invoice-component')->with([
            'tasks' => $tasks,
            'business' => $business,
            'invoice' => $invoice,
            'invoiceDataTaskCommentIds' => $invoiceDataTaskCommentIds
        ]);
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
        $this->invoiceForm->generic_comments[$key] = [];
        $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-feather-icons');
    }
    public function store()
    {
        $validated = $this->invoiceForm->validate();
        try {
            $invoice = Invoice::where('id', $this->id)->first();
            DB::beginTransaction();
            $invoice->update([
                'invoice_number' => $this->DraftInvoiceNumber,
                'currency' => $validated['currency'],
                'deduction' => $validated['deduction'],
                'total' => $validated['total_amount'],
                'notes' => $validated['description'],
                'due_at' => $validated['due_at'],
                'billed_at' => null,
                'status' => InvoiceStatus::PROCESSING->value,
            ]);
            $projectCost = 0;
            if (isset($validated['task'])) {
                foreach ($validated['task'] as $key => $task) {
                    if (array_key_exists('time', $task)) {

                        $time = 0;
                        $time = $task['time'] ?? '';

                        $comments = [];

                        $taskComments = $task['comments'] ?? [];

                        if (is_array($taskComments)) {
                            foreach ($taskComments as $commentId => $value) {
                                $time = floatval($time);
                                $comment = Comment::whereId($commentId)->first();

                                if(empty($comment)) return;

                                $comment->update(['invoiced_at' => now()]);
                                $comments[] = $comment->id;
                            }
                        }
                        $ratePerHour = null;
                        $totalCost = 0;
                        if ($this->project?->type->value === 'hourly') {
                            $totalCost = $this->project->hourly_rate * ($time / 60);
                            $ratePerHour = $this->project->hourly_rate;
                        } else if ($this->project?->type?->value === 'fixed') {
                            $totalCost = $task['task_amount'];
                        }

                        $projectCost += $totalCost;
                        $invoice->invoiceData()->updateOrCreate([
                            'task_id' => $key,
                            'time' => $time,
                            'rate_per_hour' => $ratePerHour,
                            'amount' => $totalCost,
                            'comments' => implode(',', $comments)
                        ]);
                    }
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
                foreach ($validated['generic_comments'] as $comment) {
                    $invoice->invoiceData()->updateOrCreate([
                        'time' => isset($comment['quantity']) && $comment['quantity'] ? $comment['quantity'] * 60 : 0,
                        'rate_per_hour' => isset($comment['rate']) ? $comment['rate'] : 0.00, // Provide a numeric default value
                        'amount' => isset($comment['amount']) ? $comment['amount'] : 0.00,    // Provide a numeric default value
                        'comments' => isset($comment['description']) ? $comment['description'] : ''


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
            session()->flash('success', 'Invoice created successfully.');
            return redirect()->route('dashboard.invoices.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create invoice: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => "Something went wrong"]);
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
    public function preview(Request $request)
    {
        return redirect()->route('dashboard.invoices.preview', ['data' => $this->invoiceForm, 'invoice_number' => $this->invoice_number]);
    }
    public function showElement($taskId)
    {
        $this->taskVisibility[$taskId] = true;
        $this->dispatch('reinitialize-dispatcher');
    }

    public function hideElement($taskId)
    {
        $this->taskVisibility[$taskId] = false;
        $this->dispatch('reinitialize-dispatcher');
    }
}
