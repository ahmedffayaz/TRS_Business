<?php

namespace App\Http\Controllers;

use Mail;
use App\Models\User;
use Exception;
use DataTables;
use App\Models\Comment;
use App\Company;
use App\Invoice;
use App\Models\Project;
use Carbon\Carbon;
use App\InvoiceData;
use App\InvoicePayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Jobs\SendPaymentReceivedEmail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendPaymentConfirmationEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class InvoicesController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['permission:add_invoices'], ['only' => ['store']]);
        $this->middleware(['permission:view_invoices'], ['only' => ['index', 'invoices']]);
    }

    public function index()
    {
        return view('invoices.index');
    }

    public function invoices()
    {
        $invoices = Invoice::with(['project.client_company']);

        if ($this->auth_user->hasRole('client')) {
            $invoices = $invoices->whereHas('project', function ($query) {
                $query->where('client_company_id', $this->auth_user->company_id);
            });
        }

        return DataTables::of($invoices)
            ->addColumn('client', function ($invoice) {
                if (optional($invoice->project)->client_company) {
                    $client = $invoice->project->client_company;
                    return '<a href="' . route('companies.show', $client->id) . '">' . $client->name . '</a>';
                }
                return '';
            })->addColumn('project', function ($invoice) {
            if ($invoice->project) {
                $project = $invoice->project;
                if (!$invoice->project->trashed()) {
                    return '<a href="' . route('projects.show', $project->id) . '">' . $project->name . '</a>';
                } else {
                    return $project->name;
                }
            }
            return '';
        })->addColumn('remaining_amount', function ($invoice) {
            return formatCurrency(($invoice->total + $invoice->deduction) - $invoice->paid_amount, $invoice->project->currency);
        })->editColumn('total', function ($invoice) {
            return formatCurrency(($invoice->total + $invoice->deduction), $invoice->project->currency);
        })->editColumn('paid_amount', function ($invoice) {
            return formatCurrency($invoice->paid_amount, $invoice->project->currency);
        })->editColumn('billed_at', function ($invoice) {
            return formatDate($invoice->billed_at);
        })->editColumn('status', function ($invoice) {
            return formatInvoiceStatus($invoice->status);
        })->editColumn('due_at', function ($invoice) {
            return formatDate($invoice->due_at);
        })->addColumn('actions', function ($invoice) {
            $actions = '<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push"
			          m-dropdown-toggle="click">
			            <a href="javascript:void(0)" class="m-dropdown__toggle">
			              <i class="la la-ellipsis-h"></i>
			            </a>
			            <div class="m-dropdown__wrapper">
			              <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
			                <div class="m-dropdown__inner">
			                  <div class="m-dropdown__body">
			                    <div class="m-dropdown__content">
			                      <ul class="m-nav">
						            <li class="m-nav__item"><a class="m-nav__link" href="' . asset($invoice->file) . '" target="_blank">
										<span class="m-nav__link-text">View invoice</span>
									</a></li>';
            if ($invoice->status == 'processed' || $invoice->status == 'partially_paid' || $invoice->status == 'approved') {
                $actions .= '<li class="m-nav__item"><a href="' . route('resend.email', $invoice->id) . '" class="m-nav__link"><span class="m-nav__link-text">Resend Email</span></a></li>';
            }
            if ($invoice->status == 'processed' || $invoice->status == 'partially_paid' && $this->auth_user->hasPermissionTo('bill_invoices') && is_null($invoice->billed_at)) {
                $invoice_amount = (floatval($invoice->total) + floatval($invoice->deduction)) - floatval($invoice->paid_amount);
                $actions .= '<li class="m-nav__item"><a href="javascript:void(0)" data-currency="' . strtoupper(optional($invoice->project)->currency) . '"
                        data-amount="' . $invoice_amount . '"
                        data-url="' . route('invoice.payment', $invoice->id) . '" class="m-nav__link approve-invoice"><span class="m-nav__link-text">Add Payment</span></a></li>';
            }
            if ($invoice->status == 'processed' && $this->auth_user->hasPermissionTo('add_invoices')) {
                $actions .= '<li class="m-nav__item"><a href="' . route('invoices.refresh', $invoice->id) . '" class="btn-refresh-invoice m-nav__link"><span class="m-nav__link-text">Refresh invoice</span></a></li>';
            }
            $actions .= '<li class="m-nav__item"><a href="' . route('invoice.payments', $invoice->id) . '" class="btn-list-payments m-nav__link"><span class="m-nav__link-text">Payments</span></a></li>';
            if ($invoice->status == 'processed' && $this->auth_user->hasPermissionTo('bill_invoices')) {
                $actions .= '<li class="m-nav__item"><a href="' . route('invoices.destroy', $invoice->id) . '" class="btn-delete-invoice m-nav__link"><span class="m-nav__link-text">Delete invoice</span></a></li>';
            }
            $actions .= '</ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>';
            return $actions;
        })->rawColumns(['status', 'remaining_amount', 'client', 'project', 'status', 'actions'])->addIndexColumn()->make(true);
    }

    /**
     * Save invoice data
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'due_at' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Unprocessable entity',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $inputs = $request->all();

        try {
            // throw new \Exception(json_encode($request->all()));
            $invoice_no = $this->generateUniqueInvoiceNumber();

            DB::beginTransaction();
            $project = Project::withTrashed()->findOrFail($inputs['project_id']);

            $invoice = Invoice::create([
                'project_id' => $inputs['project_id'],
                'invoice_number' => $invoice_no,
                'currency' => $project->currency,
                'deduction' => $inputs['deduction'],
                'notes' => $inputs['notes'],
                'due_at' => $inputs['due_at'],
                'send_emails' => isset($inputs['isEmail']) ? (bool) $inputs['isEmail'] : false,
            ]);

            $project_cost = 0;

            if (isset($inputs['task'])) {
                foreach ($inputs['task'] as $key => $task) {
                    $time = 0;
                    $comments = [];
                    foreach ($task['comments'] as $index => $comment_id) {
                        $time += floatval($task['time'][$index]);
                        $comment = Comment::findOrFail($comment_id);
                        $comment->update([
                            'invoiced_at' => Carbon::now(),
                        ]);
                        $comments[] = $comment->id;
                    }
                    $rate_per_hour = '';
                    $total_cost = 0;
                    if ($project->type == 'hourly') {
                        $total_cost = $task['rate_per_hour'] * ($time / 60);
                        $rate_per_hour = $task['rate_per_hour'];
                    } else if ($project->type == 'fixed') {
                        $total_cost = $task['task_amount'];
                    }
                    $project_cost += $total_cost;

                    // Update comment to invoiced
                    InvoiceData::create([
                        'invoice_id' => $invoice->id,
                        'task_id' => $key,
                        'time' => $time,
                        'rate_per_hour' => $rate_per_hour,
                        'amount' => $total_cost,
                        'comments' => implode(',', $comments),
                    ]);
                }
            }
            if (isset($inputs['generic'])) {
                foreach ($inputs['generic']['desc'] as $key => $comment) {
                    InvoiceData::create([
                        'invoice_id' => $invoice->id,
                        'time' => $inputs['generic']['qty'][$key] ? $inputs['generic']['qty'][$key] * 60 : 0,
                        'rate_per_hour' => $inputs['generic']['rate'][$key],
                        'amount' => $inputs['generic']['amount'][$key],
                        'comments' => $comment,
                    ]);
                    $project_cost += $inputs['generic']['amount'][$key];
                }
            }

            $deduction = ($project_cost === 0) ? 0 : $inputs['deduction'];
            $invoice->update([
                'total' => $project_cost + ($project_cost === 0 && $inputs['deduction'] > 0 ? $inputs['deduction'] : 0),
                'deduction' => $deduction,
            ]);

            // generate Invoice
            $invoice->update(['status' => 'processing']);
            $invoice = $invoice->with(['invoice_data.task'])->find($invoice->id);
            foreach ($invoice->invoice_data as $key => $record) {
                $comments = Comment::whereIn('id', explode(',', $record->comments))->get();
                if ($record->task) {
                    $record->task->setRelation('comments', $comments);
                }
            }

            $this->generateInvoice($invoice);

            DB::commit();

            return response()->json([
                'status_code' => JsonResponse::HTTP_OK,
                'message' => 'Invoice successfully processed',
            ], JsonResponse::HTTP_OK);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage() . ' ' . $exception->getLine(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateUniqueInvoiceNumber()
    {
        $company = $this->auth_user->company;
        $invoice_number = $company->invoice_prefix . $company->invoice_serial;
        $serial_length = strlen($company->invoice_serial);
        $serial = (int) $company->invoice_serial + 1;
        $new_serial = str_pad($serial, $serial_length, '0', STR_PAD_LEFT);
        $company->update([
            'invoice_serial' => $new_serial,
        ]);
        return $invoice_number;
    }

    /**
     * Generate invoice and save it to database
     * @param $data
     * @param bool $isEmails
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateInvoice($data, $isEmails = true)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['isPhpEnabled' => true])->setPaper('a4', 'portrait');
        $file_name = $data->invoice_number . '.pdf';
        if (!Storage::disk('public')->exists(getStoragePath('invoice'))) {
            Storage::disk('public')->makeDirectory(getStoragePath('invoice'));
        }
        $invoice = public_path('storage/' . getStoragePath('invoice')) . '/' . $file_name;
        $view = 'invoices.default.invoice';
        $pdf->loadView($view, compact('data'))->save($invoice);
        $data->update([
            'status' => 'processed',
            'file' => 'storage/' . getStoragePath('invoice') . '/' . $file_name,
        ]);
        if ($data->send_emails && $isEmails) {
            $invoice_url = url('storage/' . getStoragePath('invoice') . '/' . $file_name);
            $invoice_number = $data->invoice_number;
            $user = User::where('id', $data->project->company_id)->first();
            $u_email = $user->email;
            $data = [
                'first_name' => $user->first_name,
                'invoice_number' => $invoice_number,
            ];
            $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->get();
            $user_email = array();
            foreach ($users as $user) {
                $temp = $user->email;
                array_push($user_email, $temp);
            }
            array_push($user_email, $u_email);
            throw new \Exception(json_encode($user_email));
            Mail::send('emails.invoice', $data, function ($message) use ($invoice, $file_name, $user_email) {
                $message->attach($invoice, [
                    'as' => $file_name, // name to custom name
                    'mime' => 'application/pdf',
                ]);
                $message->from(env('MAIL_USERNAME'), 'The Right Software');

                $message->to($user_email)->subject('Invoice creation of project');
            });
        }
        return redirect()->back();
    }

    public function regenerateInvoice(int $id)
    {
        try {
            $invoice = Invoice::with(['invoice_data.task'])->findOrFail($id);
            foreach ($invoice->invoice_data as $key => $record) {
                $comments = Comment::whereIn('id', explode(',', $record->comments))->get();
                if ($record->task) {
                    $record->task->setRelation('comments', $comments);
                }
            }

            if ($invoice->total == 0) {
                // Recalculate total
                $total = 0;
                foreach ($invoice->invoice_data as $data) {
                    $total += $data->amount;
                }
                // Update invoice total
                $invoice->update([
                    'total' => $total,
                ]);
            }
            $this->generateInvoice($invoice, false);
            return response()->json([
                'status_code' => JsonResponse::HTTP_OK,
                'message' => 'Invoice refreshed successfully',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Invoice does not exists!',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $exception) {
            return response()->json([
                'status_code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function process()
    {
        $invoice = Invoice::with(['project' => function ($query) {
            $query->with([
                'company:id,name,logo,address,city,country,postal_code',
                'client_company:id,name,logo,address,city,country,postal_code',
            ]);
        }, 'invoice_data.task:name,id'])->whereStatus('pending')->first();
        if ($invoice) {
            $invoice->update(['status' => 'processing']);
            $this->generateInvoice($invoice);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $messages = [
            'billed_at.required' => 'Payment date is required',
            'billed_at.date' => 'Payment date should be a valid date',
        ];
        $validator = \Validator::make($request->all(), [
            'billed_at' => 'required|date',
        ], $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $invoice = Invoice::findOrFail($id);
            $invoice->update($request->all());
            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Invoice billed successfully!',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Invoice does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function resendEmail($id)
    {
        $data = Invoice::with(['project' => function ($query) {
            $query->with([
                'company:id,name,logo,address,city,country,postal_code',
                'client_company:id,name,logo,address,city,country,postal_code',
            ]);
        }, 'invoice_data.task:name,id'])->whereStatus('processed')->whereId($id)->first();
        $file_name = $data->invoice_number . '.pdf';
        if (!Storage::disk('public')->exists(getStoragePath('invoice'))) {
            Storage::disk('public')->makeDirectory(getStoragePath('invoice'));
        }
        $invoice = public_path('storage/' . getStoragePath('invoice')) . '/' . $file_name;
        $invoice_number = $data->invoice_number;
        $user = User::where('id', $data->project->company_id)->first();
        $u_email = $user->email;
        $data = [
            'first_name' => $user->first_name,
            'invoice_number' => $invoice_number,
        ];
        $users = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'admin');
            }
        )->get();
        $user_email = array();
        foreach ($users as $user) {
            $temp = $user->email;
            array_push($user_email, $temp);
        }
        array_push($user_email, $u_email);
        Mail::send('emails.invoice', $data, function ($message) use ($invoice, $file_name, $user_email) {
            $message->attach($invoice, [
                'as' => $file_name, // name to custom name
                'mime' => 'application/pdf',
            ]);
            $message->from(env('MAIL_USERNAME'), 'The Right Software');

            $message->to($user_email)->subject('Invoice creation of project');
        });
        return redirect()->back();
    }

    public function addInvoicePayment(Request $request, $id)
    {
        $messages = [
            'amount.required' => 'Amount is required',
            'conversion_rate.required' => 'Converted amount is required',
            'bank' => 'Bank name is required',
            'billed_at' => 'Payment date is required',
            'description' => 'Description is required',
        ];
        $validator = \Validator::make($request->all(), [
            'amount' => 'required',
            'conversion_rate' => 'required',
            'bank' => 'required',
            'billed_at' => 'required',
            'description' => 'required',
            'bank_charges' => 'required',
        ], $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $invoice = Invoice::where('id', $id)->with(['project'])->first();
            $amount = request('amount');
            $total_amount = $invoice->total + $invoice->deduction;
            $remaining_amount = $total_amount - ($invoice->invoice_payments->sum('amount') + $amount);
            $data = [
                'invoice_id' => $id,
                'amount' => request('amount'),
                'conversion_rate' => request('conversion_rate'),
                'bank' => request('bank'),
                'bank_charges' => request('bank_charges'),
                'remaining_amount' => $remaining_amount,
                'billed_at' => request('billed_at'),
                'description' => request('description'),
            ];
            $status = ($remaining_amount >= 0) ? 'paid' : 'partially_paid';
            InvoicePayment::create($data);
            $payload = [
                'status' => $status,
                'paid_amount' => $invoice->paid_amount + request('amount'),
            ];
            if ($status === "paid") {
                $payload['billed_at'] = request('billed_at');
            }
            $invoice->update($payload);
            if ($request->has('send_email')) {
                $invoice_number = $invoice->invoice_number;
                $user = User::where('company_id', $invoice->project->client_company_id)->first();
                if ($user) {
                    $client = $invoice->project->company->name;
                    $data = [
                        'first_name' => $user->first_name,
                        'client' => $client,
                        'invoice_number' => $invoice_number,
                        'received_amount' => request('amount'),
                        'payment_date' => formatDate(request('billed_at')),
                        'currency' => $invoice->project->currency,
                    ];

                    // Dispatch email to client
                    dispatch(new SendPaymentConfirmationEmail($data, $user->email));
                    // Dispatch email to admin user
                    $users = User::whereHas('roles', function ($q) {
                        $q->where('name', 'admin');
                    })->get();
                    dispatch(new SendPaymentReceivedEmail($data, $users));
                }
            }

            return response()->json([
                'status' => JsonResponse::HTTP_OK,
                'message' => 'Invoice Payment successfully!',
            ], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Invoice does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }

    public function invoicePayments(int $id)
    {
        $invoice = Invoice::with('invoice_payments')->find($id);
        $view = View::make('invoices.payments-list', ['invoice' => $invoice])->render();
        return response()->json([
            'data' => $view,
        ]);
    }

    public function destroy(int $id)
    {
        try {
            $invoice = Invoice::with(['invoice_data', 'project'])->findOrFail($id);
            // Delete invoice data first
            foreach ($invoice->invoice_data as $data) {
                if (!empty($data->comments)) {
                    $comments = explode(',', $data->comments);
                    if (count($comments)) {
                        Comment::whereIn('id', $comments)->update([
                            'invoiced_at' => null,
                        ]);
                    }
                }
            }
            $invoice->invoice_data()->delete();
            // Delete invoice file
            if (!empty($invoice->file)) {
                Storage::disk('public')->delete($invoice->file);
            }
            // Delete invoice
            $invoice->delete();
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_NOT_FOUND,
                'message' => 'Invoice does not exists!',
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status' => JsonResponse::HTTP_OK,
            'message' => 'Invoice deleted successfully',
        ], JsonResponse::HTTP_OK);
    }
}