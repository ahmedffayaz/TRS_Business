<div>
    <div class="text-center mb-2">
        <h1 class="mb-1">Create Invoice</h1>
    </div>
    <div class="mb-1">
        <b>{{ $project?->name }} - ({{ $project?->client?->name }}) - ({{ $project?->client?->business?->name }})</b>
    </div>
    <form wire:submit.prevent="createInvoice">
        <x-input type="hidden" name="project_id" id="project_id" wire:model="invoiceForm.project_id" />
        <div class="row mb-1">
            <div class="col-md-6">
                <b>Hour rate:</b> {{ formatCurrency($project?->hourly_rate, $project?->currency) }}
            </div>
            <div class="col-md-6">
                <div class="float-end">
                    <x-input-checkbox type="checkbox" id="send-emails" name="isEmail" wire:model="invoiceForm.isEmail"
                        statusClass="form-check-success" :labelValue="__('Send Emails')" />
                    @error('invoiceForm.isEmail')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-checkbox type="checkbox" id="all-comments" name="all_comments" wire:model="invoiceForm.all_comments"
                    statusClass="form-check-success" :labelValue="__('Select/De-select all comments ')" />
                @error('invoiceForm.all_comments')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <x-anchor-tag href="#" class="btn btn-primary float-end" tabindex="0" aria-controls="table-hover"
                    type="button" value="Add Generic Comment" />
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-5">
                <x-input-label for="description" class="required" value="Description" />
                <x-input type="text" name="description" id="description" placeholder="Enter description"
                    :class="$errors->has('invoiceForm.description') ? 'error' : ''" wire:model="invoiceForm.description" />
                @error('invoiceForm.description')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-2">
                <x-input-label for="quantity" class="required" value="Qty" />
                <x-input type="number" :class="$errors->has('invoiceForm.quantity') ? 'form-control error' : 'form-control'" placeholder="Quantity"
                    data-msg="Please enter quantity" min="1" autofocus wire:model="invoiceForm.quantity" />
                @error('invoiceForm.quantity')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-2">
                <x-input-label for="rate" class="required" value="Rate" />
                <x-input type="number" :class="$errors->has('invoiceForm.rate') ? 'form-control error' : 'form-control'" placeholder="Rate"
                    data-msg="Please enter rate" min="1" autofocus wire:model="invoiceForm.rate" />
                @error('invoiceForm.rate')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-2">
                <x-input-label for="amount" class="required" value="Amount" />
                <x-input type="number" :class="$errors->has('invoiceForm.amount') ? 'form-control error' : 'form-control'" placeholder="Amount"
                    data-msg="Please enter amount" min="1" autofocus wire:model="invoiceForm.amount" />
                @error('invoiceForm.amount')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-1">
                <x-anchor-tag class="btn btn-icon btn-outline-danger delete-user-fields mt-2" href="javascript:void(0);">
                    <span wire.ignore>
                        <i data-feather='trash-2'></i>
                    </span>
                </x-anchor-tag>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <x-input-label for="due-date" class="required" value="Due Date" />
                <x-input type="text" name="due_at" id="due-date" placeholder="October 14, 2020"
                    :class="$errors->has('invoiceForm.due_at') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="invoiceForm.due_at" autocomplete="off" />
                @error('invoiceForm.due_at')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-6">
                <x-input-label for="deduction" class="required" value="Amount Adjustment" />
                <x-input type="number" name="deduction" id="deduction" placeholder="Enter amount"
                    :class="$errors->has('invoiceForm.deduction') ? 'error' : ''"
                    wire:model="invoiceForm.deduction" autocomplete="off" min="0" step="0.01" />
                @error('invoiceForm.deduction')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12 mb-2">
                <span wire:ignore.>
                    <x-input-label for="notes" :value="__('Notes')" />
                    <div x-data
                        x-ref="quillEditor"
                        x-init="
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
                                @this.set('invoiceForm.notes', data)
                            });
                        "
                        wire:model.debounce.2000ms="invoiceForm.notes"
                    >{!! $invoiceForm->notes !!}</div>
                </span>
                @error('invoiceForm.notes')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 text-center">
                <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $form->isUpdate ? 'Update' : 'Add' }}</span>
                    <x-button-loader />
                </x-button>
            </div>
        </div>
    </form>
</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Initialize flatpickr
            Livewire.dispatch('flatpickr');

            $(document).on('click blur change', '.rate_per_hour', function() {
                const id = $(this).closest('tr').attr('id');
                const idParts = id.split('-');
                const taskID = idParts[1];
                calculateTaskTimeAndCost(taskID);
            });
            $(document).on('click blur change', '.task_amount', function() {
                const id = $(this).closest('tr').attr('id');
                const idParts = id.split('-');
                const taskID = idParts[1];
                calculateTaskTimeAndCost(taskID);
            });

            function calculateTaskTimeAndCost(taskID) {
                const ratePerHourUnit = $('[name="task[' + taskID + '][unit]"]').val();
                let totalTime = 0;
                $.each($('.task-' + taskID + ':checked'), function(index, item) {
                    const time = $(item).attr('data-time');
                    totalTime += Number(time);
                });
                const row = $('#task-' + taskID);
                const projectType = row.attr('data-project-type');
                let totalCost = 0;
                if (projectType === 'fixed') {
                    totalCost = Number($('[name="task[' + taskID + '][task_amount]"]').val());
                } else {
                    const ratePerHour = $('[name="task[' + taskID + '][rate_per_hour]"]').val();
                    totalCost = Number(ratePerHour) * (totalTime / 60);
                }
                row.find('.lbl-time').html(formatTime(totalTime));
                row.find('.total-cost').html(formatCurrency(totalCost, ratePerHourUnit));
                row.attr('data-sub-total', totalCost);
                calculateTotal();
            }

            function calculateTotal() {
                let total = 0;
                let unit = "";
                $('.task-item').each((index, item) => {
                    const id = $(item).attr('id');
                    if ($('.' + id + ':checked').length > 0) {
                        if (unit === "") {
                            unit = $(item).attr("data-unit");
                        }
                        total += Number.parseFloat($(item).attr('data-sub-total'), 10);
                    }
                });
                if (total > 0) {
                    $('#total').html('Total Amount: ' + formatCurrency(total, unit));
                } else {
                    $('#total').html('Total Amount: 0');
                }
            }

            function formatCurrency(amount, currency) {
                currency = currency === "EURO" ? "EUR" : currency;
                currency = currency === "Pound" ? "GBP" : currency;
                const formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: currency,
                });
                return formatter.format(amount);
            }
        })
    </script>
@endscript
