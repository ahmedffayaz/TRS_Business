@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/app-invoice.css') }}">
    <style>
        .logo {
            height: 70px;
        }

        .adjustment-amount {
            width: 95%;
            margin-left: 10px
        }

        .delete-comment {
            margin-top: 25px;
        }

        .comment-cell {
            overflow: hidden;
            text-align: center;
        }

        .comment-cell img {
            float: left;
            margin-right: 8px;
            height: 25px;
        }

        .comment-cell span {
            float: left;
        }

        .custom-width {
            width: 135px;
        }

        .card-min-height {
            min-height: 150px;
        }

        .bold {
            font-weight: bolder
        }
    </style>
@endpush
<div>
    <section class="invoice-add-wrapper">
        <form>
            <div class="row invoice-add">
                <!-- Invoice Add Left starts -->
                <div class="col-xl-9 col-md-8 col-12">
                    <div class="card invoice-preview-card">
                        <!-- Header starts -->
                        <div class="card-body invoice-padding pb-0">
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
                                <div class="invoice-number-date mt-md-0 mt-2">
                                    <div class="d-flex align-items-center justify-content-md-end mb-1">
                                        <h4 class="invoice-title">Invoice</h4>
                                        <div class="input-group input-group-merge invoice-edit-input-group">
                                            <input type="text" class="form-control invoice-edit-input" wire:model="invoice_number" value="{{ $DraftInvoiceNumber }}" />

                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="title">Due Date:</span>
                                        <x-input type="text" name="due_at" id="due_at" :class="$errors->has('invoiceForm.due_at')
                                            ? 'error flatpickr-basic form-control invoice-edit-input due-date-picker'
                                            : 'flatpickr-basic form-control invoice-edit-input due-date-picker'" wire:model="invoiceForm.due_at"
                                            value="{{ $invoiceForm->due_at ?? $invoiceDraft->due_at }}" />

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Header ends -->
                        <hr class="invoice-spacing" />
                        <!-- Address and Contact starts -->
                        <div class="card-body invoice-padding pt-0">
                            <div class="row row-bill-to invoice-spacing">
                                <div class="col-md-4 mb-lg-1">
                                    <h6 class="invoice-to-title bold">Invoice To:</h6>
                                    <div class="invoice-customer">
                                        <p>{{ $client[0] }}</p>
                                        <p>{{ $client[1] }}</p>
                                    </div>
                                </div>

                                <!-- Project Details Column (center aligned) -->
                                <div class="col-md-4 mb-lg-1">
                                    <h6 class="invoice-to-title bold">Project Details:</h6>
                                    <div class="invoice-customer">
                                        <!-- Add project details here if needed -->
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="pe-1">Project Name:</td>
                                                    <td>{{ ucwords($project?->name) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-1">Project Type:</td>
                                                    <td>{{ ucwords($project?->type->value) }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="pe-1">Project Status:</td>
                                                    <td>{{ ucwords($project?->status->value) }}</td>
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

                        <div class="">
                            <div class="card-table table-responsive card-min-height">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="d-flex">
                                                <x-input-checkbox type="checkbox" id="select-all-comments" name="tasks[]" statusClass="form-check-primary" :labelValue="__('')" />
                                                <span class="mx-1">Task</span>
                                            </th>
                                            <th>Rate Per Hour</th>
                                            <th>Time</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($tasks)
                                            @foreach ($tasks as $task)
                                                <tr class="task-{{ $task->id }}-row">
                                                    <td class="comment-cell">
                                                        @php
                                                            $isVisible = $taskVisibility[$task->id] ?? false;
                                                        @endphp
                                                        <span wire:ignore.self class="d-flex">
                                                            <a data-bs-toggle="collapse" href="#collapseElement-{{ $task->id }}" role="button"
                                                                aria-expanded="{{ $isVisible ? 'true' : 'false' }}" aria-controls="collapseElement-{{ $task->id }}"
                                                                wire:click="{{ $isVisible ? 'hideElement(' . $task->id . ')' : 'showElement(' . $task->id . ')' }}">
                                                                <i data-feather="{{ $isVisible ? 'chevron-down' : 'chevron-right' }}" class="cursor-pointer"></i>
                                                            </a>
                                                            <input type="checkbox" id="select-all-task-{{ $task->id }}-comments" name="tasks[]"
                                                                class="form-check-input mx-1 select-all-task-comments" />
                                                            {{ $task->name }}
                                                        </span>
                                                        <span class="ms-1">{{ $task->comments->count() }}</span>
                                                        <img class="rounded-circle" src="{{ asset('assets/images/Comment icon.png') }}" alt="Avatar" />
                                                    </td>
                                                    <td>
                                                        @if ($task->project->type === \App\Enums\Project\ProjectType::FIXED)
                                                            <div class="input-group input-group-merge custom-width">
                                                                <span class="input-group-text">{{ currencies($task->project->currency) }}</span>
                                                                <input type="number" class="form-control task-amount" aria-label=""
                                                                    name="task[{{ $task->id }}][task_amount]" id="task_amount_{{ $task->id }}" wire:ignore
                                                                    wire:model="invoiceForm.task.{{ $task->id }}.task_amount" value="{{ $task->project->hourly_rate }}" />
                                                            </div>
                                                        @else
                                                            <div class="input-group input-group-merge custom-width">
                                                                <span class="input-group-text">{{ currencies($task->project->currency) }}</span>
                                                                <input type="number" class="form-control task-amount" aria-label=""
                                                                    name="task[{{ $task->id }}][rate_per_hour]" id="task_amount_{{ $task->id }}"
                                                                    wire:model="invoiceForm.task.{{ $task->id }}.task_amount" value="{{ $task->project->hourly_rate }}" />
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="lbl-time" wire:ignore>{{ formatTime($invoiceForm->task[$task->id]['time']) }}</span>
                                                        <input type="hidden" class="form-control time" aria-label="" name="task[{{ $task->id }}][time]"
                                                            wire:model="invoiceForm.task.{{ $task->id }}.time" />
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="task[{{ $task->id }}][unit]" value="{{ $task->project->currency }}" />

                                                        <span class="card-text mb-0 task-total-cost" wire:ignore
                                                            id="task-{{ $task->id }}-price">{{ currencies($task->project->currency) }}
                                                            {{ number_format($this->invoiceForm->task[$task->id]['amount'] ?? 0, 2) }}
                                                            <input type="hidden" class="card-text mb-0" wire:model="invoiceForm.task.{{ $task->id }}.price"
                                                                id="task-{{ $task->id }}-price" wire:ignore />
                                                            <x-input type="hidden" name="task[{{ $task->id }}][unit]" :value="$task->project->currency"
                                                                wire:model="invoiceForm.task.{{ $task->id }}.unit" data-task-unit="{{ $task->project->currency }}" />
                                                    </td>
                                                    <x-input type="hidden" name="task[{{ $task->id }}]" :value="$task->id" />
                                                </tr>
                                                @foreach ($task->comments as $comment)
                                                    <tr id="collapseElement-{{ $task->id }}" class="collapse {{ $isVisible ? 'show' : '' }}">
                                                        <x-input type="hidden" name="task[{{ $task->id }}][time][{{ $comment->id }}]" :value="$comment->time"
                                                            wire:model="invoiceForm.task.{{ $task->id }}.time.{{ $comment->id }}" />
                                                        <td class="d-flex">
                                                            <input type="checkbox" data-time="{{ $comment?->time }}" id="toggle-comment-{{ $comment?->id }}"
                                                                class="form-check-input ms-5 toggle-comment task-{{ $task?->id }}" data-task-id="{{ $task?->id }}"
                                                                aria-label="" name="task[{{ $task?->id }}][comments][{{ $comment?->id }}]"
                                                                data-comment-id="{{ $comment?->id }}" value="{{ $comment?->id }}"
                                                                {{ in_array($comment->id, $invoiceDataTaskCommentIds) ? 'checked' : '' }} />
                                                            <span class="mx-1">{{ $comment->description }}</span>
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            <span>{{ formatTime($comment->time) }}</span>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                        @endisset
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3 ms-2">
                                <div class="col-md-12">
                                    <x-anchor-tag href="javascript:void(0);" class="btn btn-primary float-start" tabindex="0" aria-controls="table-hover" type="button"
                                        value="Add Generic Comment" id="add-generic-comments-fields" wire:click="addGenericCommentsFields({{ $i }})" />
                                </div>
                            </div>
                            <div class="row mb-1 mt-1 ms-1">
                                @foreach ($inputs as $key => $value)
                                    @include('livewire.backend.invoice.generic-comment-form', ['key' => $key])
                                @endforeach
                            </div>
                        </div>

                        <!-- Invoice Total starts -->
                        <div class="card-body invoice-padding">
                            <div class="row invoice-sales-total-wrapper">
                                <!-- First Column -->
                                <div class="col-md-6 d-flex align-items-start">
                                    <div class="mb-2">
                                        <span wire:ignore>
                                            <x-input-label for="description" :value="__('Description')" />
                                            <div x-data x-ref="quillEditor" x-init="toolbarOptions = [
                                                [
                                                    'bold', 'italic',
                                                    'underline',
                                                    'blockquote',
                                                    'code-block',
                                                    { 'header': 1 },
                                                    { 'header': 2 },
                                                    { 'list': 'ordered' },
                                                    { 'list': 'bullet' },
                                                    { 'align': [] },
                                                    'link'
                                                ],
                                            ];
                                            quill = new Quill($refs.quillEditor, {
                                                modules: {
                                                    toolbar: toolbarOptions
                                                },
                                                theme: 'snow'
                                            });
                                            quill.on('text-change', function() {
                                                data = quill.root.innerHTML;
                                                @this.set('invoiceForm.description', data)
                                            });" wire:model.debounce.2000ms="invoiceForm.description">
                                            </div>
                                        </span>
                                    </div>
                                </div>

                                <!-- Second Column -->
                                <div class="col-md-6 d-flex align-items-start justify-content-end">
                                    <div class="invoice-total-wrapper">
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Adjustment: </p>
                                            <x-input type="" name="deduction" id="deduction" data-unit="{{ isset($task) ? $tasks->project?->currency : null }}"
                                                :class="$errors->has('invoiceForm.deduction') ? 'error adjustment-amount' : 'adjustment-amount'" data-unit="{{ isset($tasks[0]) ? $tasks[0]->project?->currency : null }}"
                                                wire:model="invoiceForm.deduction" autocomplete="off" min="0" step="0.01" />
                                            @error('invoiceForm.deduction')
                                                <x-input-error :message="$message" />
                                            @enderror
                                        </div>
                                        <div class="invoice-total-item mt-1">
                                            <p class="invoice-total-title">Subtotal:</p>
                                            <p class="invoice-total-amount"> {{ currencies($invoiceForm->currency) . ' ' . number_format($invoiceForm->total_amount, 2) }}
                                            </p>
                                        </div>
                                        <hr class="my-50" />
                                        <div class="invoice-total-item">
                                            <p class="invoice-total-title">Total:</p>
                                            <p wire:ignore class="invoice-total-amount total-cost">
                                                @if (isset($invoiceForm->deduction))
                                                    {{ currencies($invoiceForm->currency) . ' ' . number_format($invoiceForm->total_amount - $invoice->deduction, 2) }}
                                                @else
                                                    {{ currencies($invoiceForm->currency) . ' ' . number_format($invoiceForm->total_amount, 2) }}
                                            </p>
                                            @endif
                                            <input type="hidden" wire:model="invoiceForm.total_amount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        </form>
        <!-- Invoice Total ends -->
</div>
</div>
<!-- Invoice Add Left ends -->

<!-- Invoice Add Right starts -->
<div class="col-xl-3 col-md-4 col-12">
    <div class="card">
        <div class="card-body">
            <button type ="button"class="btn btn-primary w-100 mb-75" wire:click="store">Send Invoice</button>
        </div>
    </div>
    <div class="mt-2">
        <p class="mb-50">Accept payments via</p>
        <select class="form-select">
            <option value="Bank Account">Bank Account</option>
            <option value="Paypal">Paypal</option>
            <option value="UPI Transfer">UPI Transfer</option>
        </select>
        <div class="invoice-terms mt-1">
            <div class="d-flex justify-content-between">
                <label class="invoice-teitle mb-0" for="paymentTerms">Payment Terms</label>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" checked id="paymentTerms" />
                    <label class="form-check-label" for="paymentTerms"></label>
                </div>
            </div>
            <div class="d-flex justify-content-between py-1">
                <label class="invoice-terms-title mb-0" for="clientNotes">Client Notes</label>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" checked id="clientNotes" />
                    <label class="form-check-label" for="clientNotes"></label>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <label class="invoice-terms-title mb-0" for="paymentStub">Payment Stub</label>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="paymentStub" />
                    <label class="form-check-label" for="paymentStub"></label>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice Add Right ends -->
</div>
</form>
</section>
</div>
@push('scripts')
    <script src="{{ asset('assets/backend/js/app-invoice.js') }}" defer></script>
    @script
        <script type="module">
            $(document).ready(function() {
                Livewire.dispatch('feather-icons');
                Livewire.on('reinitialize-icons', () => {
                    Livewire.dispatch('feather-icons');
                });

                Livewire.dispatch('flatpickr');
                Livewire.on('reinitialize-dispatcher', () => {
                    $(document).ready(function() {
                        Livewire.dispatch('flatpickr');
                        Livewire.dispatch('feather-icons');
                    });
                });

                const grandTotal = {};
                let sum = 0;
                let totalGenericCost = 0;
                let taskGenericTotal = 0;
                let adjustmentTotal = 0;
                let adjustmentUnit;
                let totalCost;

                $('#select-all-comments').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    $('.select-all-task-comments').each(function() {
                        $(this).prop('checked', isChecked).trigger('change');
                    });
                });

                $('.select-all-task-comments').on('change', function() {
                    let allChecked = true;
                    $('.select-all-task-comments').each(function() {
                        if (!$(this).is(':checked')) {
                            allChecked = false;
                        }
                    });
                    $('#select-all-comments').prop('checked', allChecked);

                    let selectAllCheckbox = $(this);
                    let taskId = selectAllCheckbox.attr('id').split('-')[3];
                    let isChecked = selectAllCheckbox.is(':checked');

                    $('.task-' + taskId).each(function() {
                        $(this).prop('checked', isChecked).trigger('change');
                    });
                });

                function recalculateTotals(unit = '') {
                    sum = Object.values(grandTotal).reduce((total, value) => total + value, 0);
                    totalGenericCost = 0;
                    $('.generic-amount').each(function() {
                        const val = parseFloat($(this).val());
                        if (!isNaN(val)) totalGenericCost += val;
                    });

                    adjustmentTotal = 0;
                    adjustmentUnit = "";
                    $('.adjustment-amount').each(function() {
                        let _self = $(this);

                        let dataUnit = _self.data('unit');
                        adjustmentUnit = dataUnit;
                        const val = parseFloat($(this).val());
                        if (!isNaN(val)) {
                            adjustmentTotal -= val;
                        }
                    });

                    taskGenericTotal = sum + totalGenericCost + adjustmentTotal;
                    subTotal = sum;

                    if (taskGenericTotal > 0 || taskGenericTotal == 0) {
                        @this.set('invoiceForm.total_amount', sum);
                        $('.subTotal').text(formatCurrency(subTotal, adjustmentUnit));
                        $('.total-cost').text(formatCurrency(taskGenericTotal, adjustmentUnit));
                        $('.adjustment-amount').removeClass('is-invalid text-danger');
                    } else if (taskGenericTotal < 0) {
                        $('.adjustment-amount').addClass('is-invalid text-danger');
                    }
                }

                $(document).on('change', '.toggle-comment', function() {
                    const taskId = $(this).data('task-id');
                    const commentId = $(this).val();
                    const taskRow = $(`.task-${taskId}-row`);

                    if (taskRow.length === 0) {
                        console.error('No rows found for task ID:', taskId);
                        return;
                    }

                    const unit = taskRow.find(`[name="task[${taskId}][unit]"]`).val();
                    if ($(this).is(':checked')) {
                        @this.set(`invoiceForm.task.${taskId}.comments.${commentId}`, true);
                        grandTotal[taskId] = calculateTaskTimeAndCost(taskId);
                    } else {
                        delete grandTotal[taskId];
                        grandTotal[taskId] = calculateTaskTimeAndCost(taskId);
                        taskRow.find('.task-total-cost').html(formatCurrency(grandTotal[taskId], unit));
                        @this.set(`invoiceForm.task.${taskId}.comments.${commentId}`, false);
                    }

                    recalculateTotals(unit);
                });

                $(document).on('keyup change', '.quantity, .rate', function() {
                    const row = $(this).closest('.row');
                    const quantity = parseFloat(row.find('.quantity').val().trim()) || 0;
                    const rate = parseFloat(row.find('.rate').val().trim()) || 0;

                    const amount = quantity * rate;
                    const toFixedAmount = amount.toFixed(2);

                    row.find('.generic-amount').val(toFixedAmount).attr('data-generic-amount', toFixedAmount);
                    const amountId = row.find('.generic-amount').attr('id');
                    @this.set(`invoiceForm.generic_comments.${amountId}.amount`, toFixedAmount);

                    recalculateTotals();
                });

                $(document).on('keyup change', '.generic-amount, .adjustment-amount', function() {
                    recalculateTotals();
                });

                $(document).on('keyup change', '.task-amount', function(event) {
                    const taskAmountID = $(this).attr('id');
                    const taskID = taskAmountID.split('_')[2];
                    const hiddenTaskInput = $(this).closest('.task-' + taskID + '-row').find('input[name^="task[' + taskID + ']"]');
                    const taskValue = hiddenTaskInput.val();

                    calculateTaskTimeAndCost(taskID);
                });

                function calculateTaskTimeAndCost(taskID) {
                    const unit = $(`[name="task[${taskID}][unit]"]`).val();
                    @this.set(`invoiceForm.task.${taskID}.unit`, unit);
                    const totalTime = $('.task-' + taskID + ':checked').toArray().reduce((total, el) => total + Number($(el).data('time')), 0);
                    const row = $('.task-' + taskID + '-row');

                    const projectType = row.data('project-type');

                    if (projectType === 'fixed') {
                        totalCost = parseFloat($(`[name="task[${taskID}][task_amount]"]`).val()) || 0;
                    } else {
                        const ratePerHour = parseFloat($(`[name="task[${taskID}][rate_per_hour]"]`).val()) || 0;
                        totalCost = ratePerHour * (totalTime / 60);
                    }
                    row.find('.lbl-time').html(formatTime(totalTime));
                    @this.set(`invoiceForm.task.${taskID}.time`, totalTime);
                    @this.set(`invoiceForm.task.${taskID}.price`, totalCost);
                    row.find('.task-total-cost').html(formatCurrency(totalCost, unit));
                    row.attr('data-task-sub-total', totalCost);

                    return totalCost;
                }

                function formatTime(time) {
                    if (time >= 60) {
                        const minutes = time % 60;
                        return `${Math.floor(time / 60)} hrs ${minutes > 0 ? minutes + ' mins' : ''}`;
                    }
                    return `${time} mins`;
                }

                function formatCurrency(amount, currency) {
                    const currencyCode = (currency && (currency === "EURO" ? "EUR" : currency === "Pound" ? "GBP" : currency)) || "USD";
                    try {
                        return new Intl.NumberFormat('en-US', {
                            style: 'currency',
                            currency: currencyCode
                        }).format(amount);
                    } catch (error) {
                        console.error(`Error formatting currency: ${currencyCode}`, error);
                        return amount;
                    }
                }
            });
        </script>
    @endscript
@endpush
