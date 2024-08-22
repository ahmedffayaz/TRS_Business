@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/app-invoice.css') }}">
    <style>
        .logo {
            height: 70px;
        }
        .bold {
            font-weight: bolder
        }
    </style>
@endpush
<div>
    <div class="invoice-preview">
        <!-- Invoice -->
        <div class="col-xl-9 col-md-8 col-12">
            <div class="card invoice-preview-card">
                <div class="card-body invoice-padding pb-0">
                    <!-- Header starts -->
                    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                        <div>
                            <div class="logo-wrapper">
                                <img src="http://localhost:8000/trs_logo.svg" alt="" class="logo">
                                <h3 class="text-primary invoice-logo">{{ $business->name }}</h3>
                            </div>
                            <p class="card-text mb-25">{{ $business->name }}</p>
                            <p class="card-text mb-25">{{ $business->address }}</p>
                            <p class="card-text mb-25">{{ $business->city }}</p>
                            <p class="card-text mb-0">+1 (123) 456 7891, +44 (876) 543 2198</p>
                        </div>
                        <div class="mt-md-0 mt-2">
                            <h4 class="invoice-title">
                                Invoice
                                <span class="invoice-number"> {{ $invoice_number }}</span>
                            </h4>
                            <div class="invoice-date-wrapper">
                                <p class="invoice-date-title">Due Date:</p>
                                <p class="invoice-date">{{ $data['due_at'] }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Header ends -->
                </div>

                <hr class="invoice-spacing" />

                <!-- Address and Contact starts -->
                <div class="card-body invoice-padding pt-0">
                    <div class="row invoice-spacing">
                        <div class="col-md-4 mb-lg-1">
                            <h6 class="invoice-to-title bold mb-2">Invoice To:</h6>
                            <div class="invoice-customer">
                                <p>{{ $client[0] }}</p>
                                <p>{{ $client[1] }}</p>
                            </div>
                        </div>

                        <!-- Project Details Column (center aligned) -->
                        <div class="col-md-4 mb-lg-1">
                            <h6 class="invoice-to-title bold mb-2">Project Details:</h6>
                            <div class="invoice-customer">
                                <!-- Add project details here if needed -->
                                <table>
                                    <tbody>
                                        <tr>
                                            <td class="pe-1">Project Name:</td>
                                            <td>{{ ucwords($project->name) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">Project Type:</td>
                                            <td>{{ ucwords($project->type->value) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="pe-1">Project Status:</td>
                                            <td>{{ ucwords($project->status->value) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Details Column -->
                        <div class="col-md-4 mb-lg-1">
                            <h6 class="mb-2 bold">Payment Details:</h6>
                            <table>
                                <tbody>
                                    <tr>
                                        <td class="pe-1">Total Due:</td>
                                        <td><strong>$12,110.55</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Bank name:</td>
                                        <td>American Bank</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">Country:</td>
                                        <td>United States</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">IBAN:</td>
                                        <td>ETD95476213874685</td>
                                    </tr>
                                    <tr>
                                        <td class="pe-1">SWIFT code:</td>
                                        <td>BR91905</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Address and Contact ends -->

                <!-- Invoice Description starts -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="py-1">Task</th>
                                <th class="py-1">Rate Per Hour</th>
                                <th class="py-1">Time</th>
                                <th class="py-1">Price</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['task'] as $task)
                                @if (isset($task['price']))
                                    <tr>
                                        <td class="py-1">
                                            <p class="card-text fw-bold mb-25">{{ $task['name'] ?? '' }}</p>
                                        </td>
                                        <td class="py-1">
                                            <span class="fw-bold">
                                                @if (isset($task['unit']) || isset($task['total_amount']))
                                                    {{ currencies($task['unit']) . $task['task_amount'] }}
                                                @endif
                                            </span>

                                        </td>
                                        <td class="py-1">
                                            @if (isset($task['time']))
                                                <span class="fw-bold">{{ formatTime($task['time']) ?? '' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-1">
                                            <span class="fw-bold">
                                                @if (isset($task['unit']) || isset($task['price']))
                                                    {{ currencies($task['unit']) . $task['price'] }}
                                                @endif
                                            </span>

                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-body invoice-padding pb-0">
                    <div class="row invoice-sales-total-wrapper">
                        <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                        </div>
                        <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                            <div class="invoice-total-wrapper">
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Adjustment:</p>
                                    @if (isset($data['deduction']))
                                        <p class="invoice-total-amount">{{ $data['deduction'] ?? '' }}</p>
                                    @endif
                                </div>
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Subtotal:</p>
                                    <p class="invoice-total-amount"> {{ currencies($data['currency']) . ' ' . number_format($data['total_amount'], 2) }}</p>
                                </div>
                                <hr class="my-50" />
                                <div class="invoice-total-item">
                                    <p class="invoice-total-title">Total:</p>
                                    @if (isset($data['deduction']))
                                        {{ currencies($data['currency']) . ' ' . number_format($data['total_amount'] - $data['deduction'], 2) }}
                                    @else
                                        {{ currencies($data['currency']) . ' ' . number_format($data['total_amount'], 2) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Invoice Description ends -->

                <hr class="invoice-spacing" />

                <!-- Invoice Note starts -->
                <div class="card-body invoice-padding pt-0">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-bold">Note:</span>
                            <span>{!! $data['description'] ?? '' !!}</span>
                        </div>
                    </div>
                </div>
                <!-- Invoice Note ends -->
            </div>
        </div>
        <!-- /Invoice -->
    </div>
</div>
@push('scripts')
    <script src="{{ asset('assets/backend/js/app-invoice.js') }}" defer></script>
@endpush
