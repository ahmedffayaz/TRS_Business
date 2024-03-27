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
                            @foreach ($invoices as $invoice)
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
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="eye" class="me-50"></i></span>
                                                    <span>View Invoice</span>
                                                </x-anchor-tag>

                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="mail" class="me-50"></i></span>
                                                    <span>Resend Email</span>
                                                </x-anchor-tag>

                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                    <span>Add Payment</span>
                                                </x-anchor-tag>

                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="refresh-cw" class="me-50"></i></span>
                                                    <span>Referesh Invoice</span>
                                                </x-anchor-tag>

                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="dollar-sign" class="me-50"></i></span>
                                                    <span>Payments</span>
                                                </x-anchor-tag>

                                                <x-anchor-tag class="dropdown-item" href="javascript:void(0);">
                                                    <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                    <span>Delete Invoice</span>
                                                </x-anchor-tag>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                </table>
                {{ $invoices->links('components.pagination') }}
            </div>
        </div>
    </div>
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
