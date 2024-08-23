<?php

namespace App\Livewire\Backend\Task;

use App\Jobs\FilesUploadJob;
use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\Invoice;
use App\Models\Project;
use Livewire\Component;
use Illuminate\Http\File;
use App\Models\Attachment;

use Livewire\Attributes\On;
use App\Mail\InvitationMail;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Jobs\ExportTaskASPdf;
use App\Traits\WithMainModal;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use App\Livewire\Forms\TaskForm;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Livewire\Forms\InvoiceForm;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Enums\Invoice\InvoiceStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Jobs\SendCreateProjectInvoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class TaskDataComponent extends Component
{
    use WithPagination, WithMainModal, WithFileUploads;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 20;

    public ?int $projectId;
    public ?string $projectSlug;

    public TaskForm $form;
    public InvoiceForm $invoiceForm;

    public bool $isTaskModalOpen = false;
    public bool $isRevenueModalOpen = false;
    public bool $isAddInvoiceModalOpen = false;
    public bool $isInviteClientModalOpen = false;
    public $project = null;
    public $tasksList = null;
    public $inputs, $i;
    public $files;
    public ?array $projectRevenue;
    public string $filePath = 'files/tasks';
    public  $slug = null;
     public $developerId = '';
    public $filterDate = '';
    public $filterProject = '';

    public $editableFiles = [];
    public $developers = [];
    public $selectedProjects = [];
    public $selectAll = false;
   public $generatedLink;
    public $client_name = null;

    public $email;

    public function mount($project = null, $projectSlug = null)
    {
        $this->projectId = $project ? $project : null;
        $this->projectSlug = $projectSlug ?? null;
        $this->inputs = [];
        $this->i = 1;
        $this->files = [];
    }

    private function getTasksQuery()
    {
        $this->dispatch('reinitialize-icons');
        $projectId = isset($this->projectId) ? $this->projectId : null;
        return Task::hasProject($projectId)->with(['project', 'comments'])
        ->when($this->developerId, function ($query) {
            $query->whereHas('user', function ($query) {
                $query->where('id', $this->developerId);
            });
        })
        ->when($this->filterDate, function ($query) {
            $dateRange = $this->filterDate;
            [$startDate, $endDate] = explode(' to ', $dateRange);
            $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            $query->whereBetween('end_date', [$startDate, $endDate]);
        })
        ->when($this->filterProject, function ($query) {
           $query->whereHas('project', function ($query) {
               $query->where('id', $this->filterProject);
           });
        })
            ->getList($this->search, $this->columnName, $this->sortDirection);
    }

    private function getTotalTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedTasks(): LengthAwarePaginator
    {
        return $this->getTasksQuery()->onlyTrashed()->paginate($this->limitPerPage);
    }

    private function getTasks(): LengthAwarePaginator
    {
        if ($this->dataCountType === 'total')
            return $this->getTotalTasks();
        else if ($this->dataCountType === 'active')
            return $this->getActiveTasks();
        else if ($this->dataCountType === 'archived')
            return $this->getArchivedTasks();
    }

    public function render()
    {
        $projectId = isset($this->projectId) ? $this->projectId : null;
        $tasks = $this->getTasks();
        $totalTasks = Task::hasProject($projectId)->withTrashed()->count();
        $totalActiveTasks = Task::hasProject($projectId)->count();
        $totalArchivedTasks = Task::hasProject($projectId)->onlyTrashed()->count();

        $projects = null;
        $user = auth()->user();
        $projects = Project::sessionBusiness()->when(!$user->hasRole('super-admin'), function ($query) use ($user) {
            $query->whereHas('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        })->pluck('name', 'id')->all();

        $members = User::sessionBusiness()->whereHas('roles', function ($query) {
                    $query->where('name', '!=', 'client');
                })->get()->pluck('nameWithDesignation', 'id');

       $developers = $this->developers;
       $is_taskComponent =  isset($this->projectSlug)  ? false :  true;
       $projectsForFilter = Task::hasProject($projectId)->with(['project'])->get();
    //    dd($projects);
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.task.task-data-component', compact('tasks', 'totalTasks', 'totalActiveTasks', 'totalArchivedTasks', 'projects', 'members', 'projectId','developers','is_taskComponent','projectsForFilter'));
    }

    public function openModal()
    {
        $this->isTaskModalOpen = true;
        $this->isRevenueModalOpen = false;
        $this->dispatch('reinitialize-dispatcher');
        $this->dispatch('resetSelectInput');
        $this->openMainModal();
        $this->dispatch('reinitialize-icons');
    }

    public function closeModal()
    {
        $this->closeMainModal();
        $this->dispatch('project-select', ['formProject' => []]);
        $this->dispatch('assigned-member-select', ['formUser' => []]);
        $this->isTaskModalOpen = false;
        $this->isRevenueModalOpen = false;
        $this->dispatch('reinitialize-icons');
    }

    public function store()
    {
        if (isset($this->projectId))
        $this->form->project_id = $this->projectId;

        $validated = $this->form->validate();
        try {
            DB::beginTransaction();
            $task = Task::create([
                'project_id' => $validated['project_id'],
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            if (!empty($validated['attachments'])) dispatch(new FilesUploadJob($validated['attachments'], $task->id));

            DB::commit();
            $this->closeModal();
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Task created successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while create task: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create task: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    #[On('edit')]
    public function edit($id)
    {
        $this->form->isUpdate = true;
        try {
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->with('attachments')->findOrFail($id);
           $this->form->set($verifiedTask);
           $this->editableFiles = $this->form->attachments;
            $this->dispatch('project-select', ['formProject' => $verifiedTask->project_id]);
            $this->dispatch('assigned-member-select', ['formUser' => $verifiedTask->user_id]);

            $this->openModal();
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while edit task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while edit task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function update($id)
    {
        $validated = $this->form->validate();

        try {
            $task = Task::select('id', 'project_id')->findOrFail($id);

            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);

            DB::beginTransaction();

            $verifiedTask->update([
                'project_id' => $validated['project_id'],
                'user_id' => $validated['user_id'],
                'name' => $validated['name'],
                'description' => $validated['description'],
                'priority' => $validated['priority'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date']
            ]);
            if (!empty($validated['attachments'])) dispatch(new FilesUploadJob($validated['attachments'], $task->id));

            DB::commit();
            $this->closeModal();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Task updated successfully.']);
        }    catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while update task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function removeFileConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'removeAttachment',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to remove this file.',
        ]);
    }

    #[On('removeAttachment')]
    public function removeFile($id)
    {
        $attachment = Attachment::where('uuid', $id)->firstOrFail();
        $getId = $attachment->attachmentable_id;
        try  {
            if(Storage::disk('public')->exists($attachment->file))
            {
                Storage::disk('public')->delete($attachment->file);
                $attachment->delete();
                $this->form->isUpdate = false;
                $this->dispatch('edit', $getId);
                $this->dispatch('alert', ['type' => 'success',  'message' => 'Attachment removed successfully.']);
            }
        }catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while update task and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }
    public function markComplete($id)
    {
        try {
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);

            DB::beginTransaction();
            $verifiedTask->update(['completed_at' => now()]);
            // Update project last updated at
            $task->project()->update(['last_updated_at' => now()]);
            DB::commit();
            $this->closeModal();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', ['type' => 'success',  'message' => 'Task marked as completed successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while task mark as completed and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while task mark as completed and task id is, ' . $id . ' error: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function archiveConfirmation($id)
    {
        $this->dispatch('swal-alert', [
            'id' => $id,
            'type' => 'archive',
            'iconType' => 'warning',
            'title' => 'Are you sure?',
            'description' => 'You are about to archive the task.',
        ]);
    }

    #[On('archive')]
    public function archive($id)
    {
        try {
            DB::beginTransaction();
            $task = Task::select('id', 'project_id')->findOrFail($id);
            $verifiedTask = Task::whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);
            $verifiedTask->delete();
            DB::commit();
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Task archived successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while task archive and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while task archive and task id is ' . $id . ' ' . $exception->getMessage());
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
            'description' => 'You are about to delete the task. This action cannot be undone.',
        ]);
    }

    #[On('delete')]
    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $task = Task::select('id', 'project_id', 'deleted_at')->onlyTrashed()->findOrFail($id);
            $verifiedTask = Task::onlyTrashed()->whereHas('project', function ($query) use ($task) {
                $query->sessionBusiness()->whereId($task->project_id);
            })->findOrFail($id);
            $verifiedTask->delete();
            DB::commit();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Task deleted successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while task delete and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while task delete and task id is ' . $id . ' ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    #[On('open-invoice-modal')]
    public function openInvoiceModal($tasks)
    {
        $this->isAddInvoiceModalOpen = true;
        $this->tasksList = $this->listWithBillableComments($tasks);
        if (!empty($this->project)) {
            $this->project = Project::sessionBusiness()->whereId($this->projectId)->first();
        }
        $this->openMainModal();
        $this->dispatch('reinitialize-flatpickr');
        $this->dispatch('reinitialize-icons');
    }

    public function closeInvoiceModal()
    {
        $this->isAddInvoiceModalOpen = false;
        $this->invoiceForm->total_amount = null;
        $this->closeMainModal();
        $this->invoiceForm->reset();
        $this->dispatch('reinitialize-icons');
    }

    public function createInvoice()
    {
        $this->invoiceForm->project_id = $this?->project?->id;
        $validated = $this->invoiceForm->validate();

        try {
            DB::beginTransaction();
            $invoiceNumber = $this->generateUniqueInvoiceNumber();
            $invoice = Invoice::create([
                'project_id' => $validated['project_id'],
                'invoice_number' => $invoiceNumber,
                'deduction' => $validated['deduction'],
                'notes' => $validated['notes'],
                'currency' => $this?->project?->currency,
                'due_at' => $validated['due_at'],
                'send_emails' => isset($validated['isEmail']) ? $validated['isEmail'] : false,
            ]);

            $projectCost = 0;
            if (isset($validated['task'])) {
                foreach ($validated['task'] as $key => $task) {
                    $time = 0;
                    $comments = [];
                    foreach ($task['comments'] as $commentId => $value) {
                        $time = floatval($task['time'][$commentId]);
                        $comment = Comment::findOrFail($commentId);
                        $comment->update(['invoiced_at' => now()]);
                        $comments[] = $comment->id;
                    }
                    $ratePerHour = null;
                    $totalCost = 0;
                    if ($this?->project?->type->value === 'hourly') {
                        $totalCost = $task['rate_per_hour'] * ($time / 60);
                        $ratePerHour = $task['rate_per_hour'];
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

            // generate Invoice
            $invoice->update(['status' => InvoiceStatus::PROCESSING->value]);
            $invoice = $invoice->with(['invoiceData.task'])->find($invoice->id);
            foreach ($invoice->invoiceData as $key => $record) {
                $comments = Comment::whereIn('id', explode(',', $record->comments))->get();
                if ($record->task) {
                    $record->task->setRelation('comments', $comments);
                }
            }

            $this->generateInvoice($invoice);

            DB::commit();
            $this->closeInvoiceModal();
            $this->dispatch('reinitialize-icons');
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Invoice created successfully.']);
        } catch (ModelNotFoundException $exception) {
            DB::rollBack();
            Log::error('Get error while create invoice: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error while create invoice: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    private function generateUniqueInvoiceNumber()
    {
        $business = $this?->project?->client?->business;
        $invoiceNumber = $business?->invoice_prefix . $business?->invoice_serial;
        $serialLength = strlen($business?->invoice_serial);
        $serial = (int) $business?->invoice_serial + 1;
        $newSerial = str_pad($serial, $serialLength, '0', STR_PAD_LEFT);
        $business->update([
            'invoice_serial' => $newSerial,
        ]);
        return $invoiceNumber;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|JsonResponse|View
     */
    private function listWithBillableComments($tasks)
    {
        try {
            if(!empty($tasks)) {
                $this->invoiceForm->total_amount = 0;
            } else {
                $this->invoiceForm->total_amount = null;
            }
            $tasks = Task::whereHas('project' , function ($query) {
                $query->sessionBusiness()->with('client');
            })->with(['project' => function ($query) {
                $query->sessionBusiness()->with('client');
            }, 'billableComments'])
                ->whereIn('id', $tasks)->get();

            foreach ($tasks as $task) {
                if ($task->billableComments) {
                    foreach ($task->billableComments as $comment) {
                        $this->invoiceForm->task[$task->id]['unit'] = $task?->project?->currency;
                        if ($task?->type?->value === 'fixed') {
                            $this->invoiceForm->task[$task->id]['task_amount'] = $task?->project?->hourly_rate;
                        } else {
                            $this->invoiceForm->task[$task->id]['rate_per_hour'] = $task?->project?->hourly_rate;
                        }
                        $this->invoiceForm->task[$task->id]['time'][$comment?->id] = $comment?->time;
                    }
                }
            }

            return $tasks;
        } catch (Exception $exception) {
            Log::error('Get error on get selected tasks with billable comments on create invoices: ' . $exception->getMessage());

            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addGenericCommentsFields($i)
    {
        $this->i = $i + 1;
        array_push($this->inputs, 1);
        $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-feather-icons');
    }

    public function removeGenericCommentsFields($key)
    {
        unset($this->inputs[$key]);
        $this->dispatch('feather-icons');
        $this->dispatch('reinitialize-feather-icons');
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

    #[On('open-revenue-modal')]
    public function openRevenueModal($data)
    {
        $this->isRevenueModalOpen = true;
        $this->projectRevenue = $data;
        $this->dispatch('open-main-modal');
    }

    public function closeRevenueModal()
    {
        $this->dispatch('close-main-modal');
        $this->isRevenueModalOpen = false;
    }

    #[On('open-invite-client-modal')]
    public function openInviteClientModal($data)
    {
        $this->slug = $data['slug'];
        $this->client_name = $data['client_name'];
        $project = Project::where('slug',$data['slug'] )->firstOrFail();
        $this->generatedLink = $project->invite_link;
        $this->isInviteClientModalOpen = true;
        $this->dispatch('open-main-modal');
    }
    #[On('close-invite-client-modal')]
    public function closeInviteClientModal()
    {
        $this->dispatch('close-main-modal');
        $this->isInviteClientModalOpen = false;
    }
   public function generateLink($slug)
    {
        $expiresAt = Carbon::now()->addWeeks(2);
        $encryptedKey = Crypt::encrypt([
            'slug' => $slug,
            'expires_at' => $expiresAt->timestamp
        ]);

        $link = url("/invite/{$encryptedKey}");
        $project = Project::where('slug', $slug)->firstOrFail();
        $project->invite_link = $link;
        $project->save();
        $this->generatedLink =  $link;
    }

    public function deleteLink($slug)
    {
        $project = Project::where('slug', $slug)->firstOrFail();
        $project->invite_link = null;
        $project->save();
        $this->generatedLink = null;
    }

    public function sendInvitationByEmail($slug)
    {
        $this->validate([
            'email' => 'required|email',
        ]);
        $this->generateLink($slug);
        Mail::to($this->email)->queue(new InvitationMail($this->generatedLink,$slug));

        session()->flash('status', 'Invitation link sent successfully!');
    }
   public function applyFilter($developerId,$date,$filterProject)
    {
        $this->developerId = $developerId;
        $this->filterDate = $date;
        $this->filterProject = $filterProject;

        $projectId = isset($this->projectId) ? $this->projectId : null;
        $filterProject = $filterProject ?? $projectId;
        $developers = User::whereHas('tasks', function ($query) use ($filterProject) {
            $query->where('project_id', $filterProject);
        })->get();

        $this->developers = $developers->isEmpty() ? null : $developers;

        if($date){
            $this->developers = null;
        }
    }
    #[On('reset-task-filter')]
    public function resetFilters()
    {
        $this->developers = null;
        $this->dispatch('reset-task-filters');
        $this->reset(['developerId','filterDate','filterProject','search']);
    }

    public function generateTaskPdf()
    {
        $groupedTasks =Task::with('project')->whereIn('id', $this->selectedProjects)->latest()->get()->groupBy('project.name');

        $pdf = App::make('dompdf.wrapper');
        $pdf->setOption(['isPhpEnable' => true])->setPaper('a4', 'portrait');
        $fileName = 'tasks_' . uniqid() . '.pdf';

        $tempDirectory = storage_path('app/public/temp/');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $filePath = $tempDirectory . $fileName;
        $view = 'livewire.backend.task.task-pdf';

        $pdf->loadView($view, compact('groupedTasks'))->save($filePath);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
