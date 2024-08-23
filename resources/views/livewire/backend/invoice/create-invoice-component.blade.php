@push('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/css/app-invoice.css') }}">
<style>
    .logo{
        height: 70px;
    }

    .adjustment-amount{
        width:60%; margin-left: 30px
    }
</style>
@endpush
<div>
    <section class="invoice-add-wrapper">
        <form wire:submit="store">
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
                                            <input type="text" class="form-control invoice-edit-input"
                                            wire:model="invoice_number" value="{{ $this->invoice_number }}"/>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="title">Due Date:</span>
                                        <x-input type="text" name="due_at" id="due_at"
                                            :class="$errors->has('invoiceForm.due_at') ? 'error flatpickr-basic form-control invoice-edit-input due-date-picker' : 'flatpickr-basic form-control invoice-edit-input due-date-picker'"
                                            wire:model="invoiceForm.due_at" value="{{ $this->invoiceForm->due_at }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Header ends -->
                        <hr class="invoice-spacing" />
                        <!-- Address and Contact starts -->
                        <div class="card-body invoice-padding pt-0">
                            <div class="row row-bill-to invoice-spacing">
                                {{-- @foreach($tasks as $task) --}}
                                <div class="col-xl-8 mb-lg-1 col-bill-to ps-0">
                                    <h6 class="invoice-to-title">Invoice To:</h6>
                                    <div class="invoice-customer">
                                        <p>{{ $clientName[0] }}</p>
                                        <p>{{ $clientName[1] }}</p>
                                    </div>
                                </div>
                                {{-- @endforeach --}}
                                <div class="col-xl-4 p-0 ps-xl-2 mt-xl-\">
                                    <h6 class="mb-2">Payment Details:</h6>
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

                        <!-- Product Details starts -->
                        <div class="card-body invoice-padding invoice-product-details">
                            <div class=" mb-3">
                                <div class="row">
                                    <div class="col-12 d-flex align-items-center">
                                        <x-input-checkbox type="checkbox" id="select-all-comments"
                                            name="tasks[]"
                                            statusClass="form-check-primary" :labelValue="__('')" />
                                        <p class="mb-0 ms-2">Select all Comments</p>
                                    </div>
                                </div>
                            </div>
                            <form class="source-item">
                                @foreach($tasks as $task)
                                    <div data-repeater-list="group-a">
                                        <div class="repeater-wrapper task-{{ $task->id }}-row" id="task-{{ $task->id }} totals" data-repeater-item data-project-type="{{ $task->project?->type }}" wire:ignore.self
                                            data-task-sub-total="0" data-unit="{{ $task->project?->currency }}">
                                            <div class="row mt-3">
                                                <div class="col-12 d-flex product-details-border position-relative pe-0">
                                                    <div class="row w-100 pe-lg-0 pe-1 mt-2">
                                                        <div class="col-lg-5 mb-lg-0 mb-2 mt-lg-0 mt-2">
                                                            <p class="card-text col-title mb-md-50 mb-0">Task</p>
                                                            <input type="text" id="country" name="task" value="{{ $task->name }}" readonly>
                                                            <div class="col mt-3">
                                                                <x-input type="hidden" name="task[{{ $task?->id }}]" :value="$task?->id" />
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-3 col-12 my-lg-0 my-2">
                                                            <p class="card-text col-title mb-md-2 mb-0">Time</p>
                                                            <span class="lbl-time" wire:ignore.>0 mins</span>
                                                            <input type="hidden" class="form-control time" aria-label=""
                                                            name="task[{{ $task?->id }}][time]"
                                                            wire:model="invoiceForm.task.{{ $task?->id }}.time"/>
                                                        </div>
                                                        @if ($task->project->type === \App\Enums\Project\ProjectType::FIXED)
                                                        <div class="col-lg-2 col-12 my-lg-0 my-2">
                                                            <p class="card-text col-title mb-md-2 mb-0">Rate Per Hour</p>
                                                            <input type="number" class="form-control task-amount" aria-label=""
                                                            name="task[{{ $task?->id }}][task_amount]" id="task_amount_{{ $task?->id }}"
                                                            wire:model="invoiceForm.task.{{ $task?->id }}.task_amount"
                                                            value="{{ $task?->project?->hourly_rate }}" />
                                                        </div>
                                                        @else
                                                        <div class="col-lg-2 col-12 my-lg-0 my-2">
                                                            <p class="card-text col-title mb-md-2 mb-0">Rate Per Hour</p>
                                                            <input type="number" class="form-control task-amount" aria-label=""
                                                            name="task[{{ $task?->id }}][rate_per_hour]" id="task_amount_{{ $task?->id }}"
                                                            wire:model="invoiceForm.task.{{ $task?->id }}.task_amount"
                                                            value="{{ $task?->project?->hourly_rate }}" />
                                                        </div>
                                                        @endif
                                                        <input type="hidden" name="task[{{ $task->id }}][unit]"
                                                            value="{{ $task->project->currency }}" />
                                                            <div class="col-lg-2 col-12 mt-lg-0 mt-2">
                                                                <p class="card-text col-title mb-md-50 mb-0">Price</p>
                                                                <span class="card-text mb-0 task-total-cost" id="task-{{ $task->id }}-price" wire:ignore.>{{ currencies($task->project->currency) }}0.00</span>
                                                            </div>
                                                        </div>
                                                        <x-input type="hidden" name="task[{{ $task?->id }}][unit]" :value="$task?->project?->currency"
                                                            wire:model="invoiceForm.task.{{ $task?->id }}.unit" data-task-unit="{{ $task?->project?->currency }}" />
                                                </div>

                                                    <b class="mt-1">Comments</b>
                                                    @foreach ($task->comments as $comment)
                                                        <div class="container">
                                                            <div class="row align-items-center">
                                                                <div class="col-12 col-sm-6 col-md-auto d-flex align-items-center mt-1">
                                                                    <x-input type="hidden" name="task[{{ $task?->id }}][time][{{ $comment?->id }}]"
                                                                        :value="$comment?->time" wire:model="invoiceForm.task.{{ $task?->id }}.time.{{ $comment?->id }}" />
                                                                        <div class="form-check form-check-inline">
                                                                            <input type="checkbox" data-time="{{ $comment?->time }}" id=".toggle-comment"
                                                                                class="form-check-input toggle-comment task-{{ $task?->id }}" data-task-id="{{ $task?->id }}"
                                                                                aria-label="" name="task[{{ $task?->id }}][comments][{{ $comment?->id }}]"
                                                                                wire:model="invoiceForm.task.{{ $task?->id }}.comments.{{ $comment?->id }}"
                                                                                data-comment-id="{{ $comment?->id }}" value="{{ $comment?->id }}" />
                                                                        </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-8">
                                                                    <p class=" py-1">{{ $comment->description}}</p>
                                                                </div>
                                                                <div class="col-12 col-md ms-auto text-end">
                                                                    <p class="mt-1 mb-0"><b>Time Spent: </b>
                                                                        <span>{{ formatTime($comment->time) }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        <div class="row mt-1">
                                            <div class="col-md-12">
                                                <x-anchor-tag href="javascript:void(0);"
                                                    class="btn btn-primary float-start" tabindex="0"
                                                    aria-controls="table-hover" type="button"
                                                    value="Add Generic Comment" id="add-generic-comments-fields"
                                                    wire:click="addGenericCommentsFields({{ $i }})" />
                                            </div>
                                        </div>
                                        <div class="row mb-1 mt-1">
                                            @foreach($inputs as $key => $value)
                                                @include('livewire.backend.invoice.generic-comment-form', ['key' => $key])
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- Product Details ends -->

                                    <!-- Invoice Total starts -->
                                    <div class="card-body invoice-padding">
                                        <div class="row invoice-sales-total-wrapper">
                                            <!-- First Column -->
                                            <div class="col-md-6 d-flex align-items-start">
                                                <div class="mb-2">
                                                    <label for="note" class="form-label fw-bold">Note:</label>
                                                    <span wire:ignore>
                                                        <x-input-label for="description" :value="__('Description')" />
                                                        <div x-data x-ref="quillEditor" x-init="
                                                                toolbarOptions = [
                                                                    [
                                                                        'bold', 'italic',
                                                                        'underline',
                                                                        'blockquote',
                                                                        'code-block',
                                                                        { 'header': 1 },
                                                                        { 'header': 2 },
                                                                        { 'list': 'ordered'},
                                                                        { 'list': 'bullet' },
                                                                        { 'align': [] },
                                                                        'link'
                                                                    ],
                                                                ];
                                                                quill = new Quill($refs.quillEditor, {modules: {
                                                                    toolbar: toolbarOptions
                                                                },theme: 'snow'});
                                                                quill.on('text-change', function () {
                                                                    data = quill.root.innerHTML;
                                                                    @this.set('invoiceForm.description', data)
                                                                });
                                                            " wire:model.debounce.2000ms="invoiceForm.description"></div>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Second Column -->
                                            <div class="col-md-6 d-flex align-items-start justify-content-end">
                                                <div class="invoice-total-wrapper">
                                                    <div class="invoice-total-item">
                                                        {{-- {{ dd( ($task) ? $task->project?->currency : null) }} --}}
                                                        <p class="invoice-total-title">Adjustment: </p>
                                                        {{-- <p wire:ignore class="invoice-total-amount total-cost">0</p> --}}
                                                        <x-input type="" name="deduction" id="deduction"
                                                        data-unit="{{ isset($task) ? $tasks->project?->currency : null }}"
                                                        :class="$errors->has('invoiceForm.deduction') ? 'error adjustment-amount' : 'adjustment-amount'"  data-unit="{{ isset($tasks[0]) ? $tasks[0]->project?->currency : null }}"
                                                        wire:model="invoiceForm.deduction" autocomplete="off" min="0" step="0.01" />
                                                        @error('invoiceForm.deduction')
                                                            <x-input-error :message="$message" />
                                                        @enderror
                                                    </div>
                                                    <div class="invoice-total-item mt-1">
                                                        <p class="invoice-total-title">Subtotal:</p>
                                                        <p wire:ignore class="invoice-total-amount total-cost">0</p>
                                                    </div>
                                                    <hr class="my-50" />
                                                    <div class="invoice-total-item">
                                                        <p class="invoice-total-title">Total:</p>
                                                        <p wire:ignore class="invoice-total-amount total-cost">0</p>
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
                            <button class="btn btn-primary w-100 mb-75" disabled>Send Invoice</button>
                            <a href="./app-invoice-preview.html" class="btn btn-outline-primary w-100 mb-75">Preview</a>
                            <button type="submit" class="btn btn-outline-primary w-100">Save</button>
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
        <!-- /Add New Customer Sidebar -->
    </section>
</div>
@push('scripts')
<script src="{{ asset('assets/backend/js/app-invoice.js') }}" defer></script>
@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.dispatch('feather-icons');
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });


            // Reinitialize flatpickr
            Livewire.dispatch('flatpickr');
            Livewire.on('reinitialize-dispatcher', () => {
                $(document).ready(function () {
                    Livewire.dispatch('flatpickr');
                    Livewire.dispatch('feather-icons');
                });
            });
        });

        const grandTotal = {};
        let sum = 0;
        let totalGenericCost = 0;
        let taskGenericTotal = 0;
        let adjustmentTotal = 0;
        let adjustmentUnit;

        // Event handler for "Select all Comments" checkbox
        $('#select-all-comments').on('change', function() {
            // Check if the checkbox is checked
            const isChecked = $(this).is(':checked');

            // Iterate through each task's comments
            $('.toggle-comment').each(function() {
                // Check/uncheck each comment checkbox
                $(this).prop('checked', isChecked).trigger('change');
            });
        });

        // Function to recalculate totals and update the display
        function recalculateTotals(unit = '') {
            // Recalculate grand total from the checked tasks
            sum = Object.values(grandTotal).reduce((total, value) => total + value, 0);

            // Recalculate total generic cost
            totalGenericCost = 0;
            $('.generic-amount').each(function() {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) totalGenericCost += val;
            });

            // Recalculate total adjustment cost
            adjustmentTotal = 0;
            adjustmentUnit = "";
            $('.adjustment-amount').each(function() {
                let _self = $(this);
                let dataUnit = _self.data('unit');
                adjustmentUnit = dataUnit;
                const val = parseFloat($(this).val());
                if (!isNaN(val)) adjustmentTotal -= val;
            });

            // Recalculate task generic total
            taskGenericTotal = sum + totalGenericCost + adjustmentTotal;

            // Update total amount and display
            @this.set('invoiceForm.total_amount', sum);
            $('.total-cost').text(formatCurrency(taskGenericTotal, adjustmentUnit));
        }

        // Event handler for checkbox changes
        $(document).on('change', '.toggle-comment', function() {
            const taskId = $(this).data('task-id');
            const taskRow = $('.task-' + taskId + '-row');
            const unit = taskRow.find('[name="task[' + taskId + '][unit]"]').val(); // Get unit from task row

            if ($(this).is(':checked')) {
                grandTotal[taskId] = calculateTaskTimeAndCost(taskId);
            } else {
                delete grandTotal[taskId];
                grandTotal[taskId] = 0; // Explicitly set to 0 for clarity
                taskRow.find('.task-total-cost').html(formatCurrency(0, unit));
            }

            recalculateTotals(unit);
        });

        // Event handler for quantity and rate changes
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

        // Event handler for changes to .generic-amount and .adjustment-amount
        $(document).on('keyup change', '.generic-amount, .adjustment-amount', function() {
            recalculateTotals();
        });

        $(document).on('keyup change', '.task-amount', function(event) {
            // Get the ID of the current input field
            const taskAmountID = $(this).attr('id');

            // Extract the task ID from the ID of the current input field
            const taskID = taskAmountID.split('_')[2]; // Assuming the format is 'task_amount_<id>'

            // Find the related hidden input field in the same context
            const hiddenTaskInput = $(this).closest('.task-' + taskID + '-row').find('input[type="hidden"][name^="task[' + taskID + ']"]');
            const taskValue = hiddenTaskInput.val();

            // Call your custom calculation function
            calculateTaskTimeAndCost(taskID);
        });

        // Function to calculate task time and cost
        function calculateTaskTimeAndCost(taskID) {
            const unit = $(`[name="task[${taskID}][unit]"]`).val();
            @this.set(`invoiceForm.task.${taskID}.unit`, unit);
            const totalTime = $('.task-' + taskID + ':checked').toArray().reduce((total, el) => total + Number($(el).data('time')), 0);
            const row = $('.task-' + taskID + '-row');
            const projectType = row.data('project-type');
            let totalCost = 0;

            if (projectType === 'fixed') {
                totalCost = parseFloat($(`[name="task[${taskID}][task_amount]"]`).val()) || 0;

            } else {
                const ratePerHour = parseFloat($(`[name="task[${taskID}][rate_per_hour]"]`).val()) || 0;
                totalCost = ratePerHour * (totalTime / 60);
            }

            row.find('.lbl-time').html(formatTime(totalTime));
            @this.set(`invoiceForm.task.${taskID}.time`, totalTime);
            row.find('.task-total-cost').html(formatCurrency(totalCost, unit));

            // Update data-sub-total
            row.attr('data-task-sub-total', totalCost);

            return totalCost;
        }

        // Function to format time
        function formatTime(time) {
            if (time >= 60) {
                const minutes = time % 60;
                return `${Math.floor(time / 60)} hrs ${minutes > 0 ? minutes + ' mins' : ''}`;
            }
            return `${time} mins`;
        }

        // Function to format currency
        function formatCurrency(amount, currency) {
            const currencyCode = currency === "EURO" ? "EUR" : currency === "Pound" ? "GBP" : currency;
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: currencyCode }).format(amount);
        }

    </script>
@endscript
@endpush
