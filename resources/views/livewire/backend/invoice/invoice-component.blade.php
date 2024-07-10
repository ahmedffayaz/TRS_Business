<div>
    @section('breadcrumbs', Breadcrumbs::render('invoices'))
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Invoices</h4>
        </div>
        <div class="card-body py-1 my-25">
            @php
                $dataCount = [
                    'total' => $totalInvoices,
                    'active' => $activeInvoices,
                    'archived' => $archivedInvoices,
                ];
            @endphp

            <x-table-search :dataCounter="$dataCount" />

            <div class="card-table table-responsive card-min-height">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Invoice Number</th>
                            <th>Total Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($invoices)
                        @php
                            $pendingStatus = \App\Enums\Invoice\InvoiceStatus::PENDING->value;
                            $processingStatus = \App\Enums\Invoice\InvoiceStatus::PROCESSING->value;
                            $processedStatus = \App\Enums\Invoice\InvoiceStatus::PROCESSED->value;
                            $partiallyPaidStatus = \App\Enums\Invoice\InvoiceStatus::PARTIALLYPAID->value;
                            $paidStatus = \App\Enums\Invoice\InvoiceStatus::PAID->value;
                            $approvedStatus = \App\Enums\Invoice\InvoiceStatus::APPROVED->value;
                        @endphp
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="sorting_1">
                                        <x-anchor-tag href="#">{{ $invoice?->project?->client?->name }}</x-anchor-tag>
                                    </td>
                                    <td>
                                        <x-anchor-tag
                                            href="{{ route('dashboard.projects.detail', $invoice?->project?->slug) }}">{{ $invoice?->project?->name }}</x-anchor-tag>
                                    </td>
                                    <td>
                                        {{ $invoice?->invoice_number }}
                                    </td>
                                    <td>{{ formatCurrency($invoice->total + $invoice->deduction, $invoice->project->currency) }}
                                    </td>
                                    <td>
                                        {{ formatCurrency($invoice->total + $invoice->deduction - $invoice->paid_amount, $invoice->project->currency) }}
                                    </td>
                                    <td>{!! formatInvoiceStatus($invoice?->status?->value) !!}</td>
                                    <td>{{ formatDate($invoice?->due_at) }}</td>
                                    <td>
                                        <div class="dropdown position-static">
                                            @if (empty($invoice?->deleted_at))
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <x-anchor-tag class="dropdown-item" href="{{ asset($invoice?->file) }}" target="_blank">
                                                        <span wire:ignore><i data-feather="eye" class="me-50"></i></span>
                                                        <span>View Invoice</span>
                                                    </x-anchor-tag>

                                                    @if ($invoice?->status?->value === $processedStatus || $invoice?->status?->value === $partiallyPaidStatus || $invoice?->status?->value === $approvedStatus)
                                                        <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="resendEmail({{ $invoice?->id }})">
                                                            <span wire:ignore><i data-feather="mail" class="me-50"></i></span>
                                                            <span>Resend Email</span>
                                                        </x-anchor-tag>
                                                    @endif

                                                    @if ($invoice?->status?->value === $processedStatus || $invoice?->status?->value === $partiallyPaidStatus  && auth()->user()->hasPermissionTo('bill_invoices') && is_null($invoice?->billed_at))
                                                    <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="openAddPaymentModal({{ $invoice?->id }})">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Add Payment</span>
                                                    </x-anchor-tag>
                                                    @endif

                                                    @if ($invoice?->status?->value === $processedStatus && auth()->user()->hasPermissionTo('add_invoices'))
                                                        <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="regenerateInvoice({{ $invoice?->id }})">
                                                            <span wire:ignore><i data-feather="refresh-cw" class="me-50"></i></span>
                                                            <span>Referesh Invoice</span>
                                                        </x-anchor-tag>
                                                    @endif

                                                    <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="showPayments({{ $invoice?->id }})">
                                                        <span wire:ignore><i data-feather="dollar-sign" class="me-50"></i></span>
                                                        <span>Payments</span>
                                                    </x-anchor-tag>

                                                    @if ($invoice?->status?->value === $processedStatus && auth()->user()->hasPermissionTo('bill_invoices'))
                                                        <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation({{ $invoice?->id }})">
                                                            <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                            <span>Delete Invoice</span>
                                                        </x-anchor-tag>
                                                    @endif
                                                @else
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <span wire:ignore.>
                                                            <i data-feather='lock'></i>
                                                        </span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="no-hover">
                                    <td colspan="8" class="text-center py-1 fw-bold">
                                        <p>No Invoice Found</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endisset
                    </tbody>
                </table>
                {{ $invoices->links('components.pagination') }}
            </div>
        </div>
    </div>

    @if ($invoiceAddPayment)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="modal-md" closeModal="closeModal">
            @include('livewire.backend.invoice.add-payment-form')
        </x-main-modal>
    @elseif ($invoicePaymentDetail)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal">
            @include('livewire.backend.invoice.payment-detail')
        </x-main-modal>
    @else
        <x-main-modal wireIgnoreSelf="wire:ignore.self">
        </x-main-modal>
    @endif
</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.dispatch('feather-icons');
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });
        });
    </script>
@endscript
