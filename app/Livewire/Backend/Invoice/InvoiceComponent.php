<?php

namespace App\Livewire\Backend\Invoice;

use Exception;
use App\Models\User;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\WithMainModal;
use App\Models\InvoicePayment;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Enums\Invoice\InvoiceStatus;
use Illuminate\Support\Facades\Storage;
use App\Livewire\Forms\InvoicePaymentForm;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('Invoices')]
class InvoiceComponent extends Component
{
    use WithPagination, WithMainModal;

    public $business_id;

    public string $search = '';
    public string $columnName = 'created_at';
    public string $sortDirection = 'desc';
    public string $dataCountType = 'total'; // Default user type

    public int $limitPerPage = 20;

    public bool $invoiceAddPayment = false;
    public bool $invoicePaymentDetail = false;
    public $invoiceId = null;
    public ?string $currency;

    public $invoicePayments = null;

    public InvoicePaymentForm $invoicePaymentForm;

    private function getInvoiceQuery()
    {
        $this->search ? $this->resetPage() : ''; // reset pagination while searching

        return Invoice::whereHas('project', function ($query) {
            $query->sessionBusiness();
        })->getList($this->search, $this->columnName, $this->sortDirection);
    }

    private function getTotalInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->withTrashed()->paginate($this->limitPerPage);
    }

    private function getActiveInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->paginate($this->limitPerPage);
    }

    private function getArchivedInvoices(): LengthAwarePaginator
    {
        return $this->getInvoiceQuery()->onlyTrashed()->paginate($this->limitPerPage);
    }

    private function getInvoices(): LengthAwarePaginator
    {
        if ($this->dataCountType === 'active') {
            return $this->getActiveInvoices();
        } elseif ($this->dataCountType === 'archived') {
            return $this->getArchivedInvoices();
        } else {
            return $this->getTotalInvoices();
        }
    }

    public function openModal()
    {
        $this->openMainModal();
        $this->dispatch('reinitialize-icons');
        $this->dispatch('reinitialize-flatpickr');
    }

    public function closeModal()
    {
        $this->closeMainModal();
        $this->invoicePaymentForm->reset();
        $this->invoiceAddPayment = false;
        $this->invoiceId = null;
        $this->currency = '';
        $this->invoicePaymentDetail = false;
        $this->invoicePayments = null;
    }

    public function openAddPaymentModal($id)
    {
        $this->invoiceAddPayment = true;
        $this->invoiceId = $id;
        try {
            $invoice = Invoice::whereHas('project', function ($query) {
                $query->sessionBusiness();
            })->findOrFail($id);

            $this->currency = currencies($invoice?->project?->currency);
            $this->openModal();
        } catch (Exception $exception) {
            Log::error('Get error on open add invoice payment modal: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function createInvoicePayment($id)
    {
        $validated = $this->invoicePaymentForm->validate();

        try {
            $invoice = Invoice::whereHas('project', function ($query) {
                $query->sessionBusiness();
            })->findOrFail($id);

            $amount = $validated['amount'];
            $totalAmount = $invoice?->total + $invoice?->deduction;
            $remainingAmount = $totalAmount - ($invoice?->invoicePayments->sum('amount') + $amount);

            $data = [
                'invoice_id' => $id,
                'amount' => $validated['amount'],
                'conversion_rate' => $validated['conversion_rate'],
                'bank' => $validated['bank'],
                'bank_charges' => $validated['bank_charges'],
                'remaining_amount' => $remainingAmount,
                'billed_at' => $validated['billed_at'],
                'description' => $validated['notes']
            ];

            $status = ($remainingAmount === 0 || $remainingAmount === '0.0' || $remainingAmount === '0.00')
                ? InvoiceStatus::PAID->value : InvoiceStatus::PARTIALLYPAID->value;

            DB::beginTransaction();

            $invoicePayment = InvoicePayment::create($data);
            $data['payment_id'] = $invoicePayment->id;

            $this->generatePaymentPdf($data, $invoice?->created_at, $invoicePayment);

            $payload = [
                'status' => $status,
                'paid_amount' => $invoice->paid_amount + $validated['amount'],
            ];

            if ($status === InvoiceStatus::PAID->value) {
                $payload['billed_at'] = $validated['billed_at'];
            }

            $invoice->update($payload);

            DB::commit();
            $this->closeModal();

            if ($validated['send_email']) {
                $invoice_number = $invoice->invoice_number;
                $user = User::where('client_id', $invoice->project->client_id)->first();
                if ($user) {
                    $data = [
                        'first_name' => $user->first_name,
                        'client' => $invoice->project->client->name,
                        'invoice_number' => $invoice_number,
                        'received_amount' => $validated['amount'],
                        'payment_date' => formatDate($validated['billed_at']),
                        'currency' => $invoice->project->currency,
                    ];

                    // Dispatch email to client
                    // dispatch(new SendPaymentConfirmationEmail($data, $user->email));
                    // Dispatch email to admin user
                    $users = User::whereHas('roles', function ($q) {
                        $q->where('name', 'admin')->orWhere('name', 'super-admin');
                    })->get();
                    // dispatch(new SendPaymentReceivedEmail($data, $users));
                }
            }
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Invoice payment added successfully.']);
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error('Get error on add invoice payment: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function generatePaymentPdf($data, $issueDate, $invoicePayment = null)
    {
        $invoice = getInvoiceRecord($data['invoice_id']);
        $pdf = App::make('dompdf.wrapper');
        $pdf->setOptions(['isPhpEnabled' => true])->setPaper('a4', 'portrait');
        $fileName ='Payment_invoice'.$data['payment_id']. '_'.$data['invoice_id']. '.pdf';
        if (!Storage::disk('public')->exists(getStoragePath('payment'))) {
            Storage::disk('public')->makeDirectory(getStoragePath('payment'));
        }
        $payments = public_path('storage/' . getStoragePath('payment')) . '/' . $fileName;
        $view = 'livewire.backend.invoice.payment-pdf';
        $pdf->loadView($view,compact('data', 'issueDate'))->save($payments);

        $invoicePayment->update([
            'file' => 'storage/' . getStoragePath('payment') . '/' . $fileName,
        ]);
    }

    public function showPayments($id)
    {
        $this->invoicePaymentDetail = true;

        try {
            $this->invoicePayments = Invoice::whereHas('project', function ($query) {
                $query->sessionBusiness();
            })->with(['invoicePayments'])->findOrFail($id);
            $this->openModal();
        } catch (Exception $exception) {
            Log::error('Get error on open show invoice payments: ' . $exception->getMessage());
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    public function render()
    {
        $invoices = $this->getInvoices();
        $totalInvoices = Invoice::withTrashed()->count();
        $activeInvoices = Invoice::count();
        $archivedInvoices = Invoice::onlyTrashed()->count();
        $this->dispatch('reinitialize-icons');
        return view('livewire.backend.invoice.invoice-component', compact('invoices', 'totalInvoices', 'activeInvoices', 'archivedInvoices'));
    }
}
