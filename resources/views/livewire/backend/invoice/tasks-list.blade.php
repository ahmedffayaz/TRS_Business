@assets
    <style></style>
@endassets
<table class="table table-sm table-borderless invoice-table" data-total-billable-tasks="">
    <tbody>
        @foreach ($billableTasks as $task)
            @if (count($task?->billableComments))
                @php
                    $taskStatus = $task?->completed_at ? 'completed' : 'active';
                @endphp
                <tr class="task-item bg-light" id="task-{{ $task?->id }}" data-unit="{{ $task?->project?->currency }}"
                    data-sub-total="0" data-project-type="{{ $task?->project?->type }}">
                    <td>
                        <span class="badge bg-primary fs-5">Task</span>
                    </td>
                    <td>
                        {!! formatTaskCompletedStatus($taskStatus) !!}
                    </td>
                    <td>
                        <p class="mb-0">{{ $task?->name }}</p>
                    </td>
                    <td>
                        <span class="lbl-time" wire:ignore>0 mins</span>
                    </td>
                    <td class="input-width">
                        <x-input type="hidden" name="task[{{ $task?->id }}]" :value="$task?->id" />
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">{{ currencies($task?->project?->currency) }}</span>
                            @if ($task?->project?->type?->value === 'hourly')
                                <input type="number" class="form-control rate_per_hour" aria-label=""
                                    name="task[{{ $task?->id }}][rate_per_hour]" value="{{ $task?->project?->hourly_rate }}"
                                    wire:model="invoiceForm.task.{{ $task?->id }}.rate_per_hour"
                                    data-task-hourly_rate="{{ $task?->project?->hourly_rate }}" />
                            @else
                                <input type="number" class="form-control task_amount" aria-label=""
                                    name="task[{{ $task?->id }}][task_amount]" id="task_amount_{{ $task?->id }}"
                                    wire:model="invoiceForm.task.{{ $task?->id }}.task_amount"
                                    value="{{ $task?->project?->hourly_rate }}" />
                            @endif
                        </div>
                        <x-input type="hidden" name="task[{{ $task?->id }}][unit]" :value="$task?->project?->currency"
                            wire:model="invoiceForm.task.{{ $task?->id }}.unit" data-task-unit="{{ $task?->project?->currency }}" />
                    </td>
                    <td>
                        <span class="total-cost" wire:ignore></span>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <th colspan="3">Comments</th>
                    <th style="white-space: nowrap;">Time Spent</th>
                </tr>
                @foreach ($task?->billableComments as $comment)
                    <tr>
                        <td class="text-center">
                            <x-input type="hidden" name="task[{{ $task?->id }}][time][{{ $comment?->id }}]"
                                :value="$comment?->time" wire:model="invoiceForm.task.{{ $task?->id }}.time.{{ $comment?->id }}" />
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" data-time="{{ $comment?->time }}"
                                        class="form-check-input toggle-comment task-{{ $task?->id }}" data-task-id="{{ $task?->id }}"
                                        aria-label="" name="task[{{ $task?->id }}][comments][{{ $comment?->id }}]"
                                        wire:model="invoiceForm.task.{{ $task?->id }}.comments.{{ $comment?->id }}"
                                        data-comment-id="{{ $comment?->id }}" value="{{ $comment?->id }}" />
                                </div>
                        </td>
                        <td colspan="3">{{ $comment?->description }}</td>
                        <td>{{ formatTime($comment?->time) }}</td>
                    </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>
<span wire:ignore.>
<div class="fw-bolder text-end mb-1" id="total"></div>
</span>
<x-input type="hidden" name="total_amount" value="" wire:model="invoiceForm.total_amount" />
@error('invoiceForm.total_amount')
    <x-input-error :message="$message" />
@enderror

@script
    <script type="module">
        $(document).ready(function () {
            // Toggle comments
            $('#select-all-comments').on('click', function() {
                $('.toggle-comment').trigger('click');
            });
            $(document).on('click', '.toggle-comment', function() {
                const taskID = $(this).attr('data-task-id');
                calculateTaskTimeAndCost(taskID);
                if ($('.toggle-comment:checked').length === 0) {
                    document.getElementById("select-all-comments").checked = false;
                } else if ($('.toggle-comment:checked').length === $('.toggle-comment').length) {
                    document.getElementById("select-all-comments").checked = true;
                }
            });
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
                const ratePerHourUnitData = $('[name="task[' + taskID + '][unit]"]').attr('data-task-unit');
                @this.set('invoiceForm.task.'+taskID+'.unit', ratePerHourUnitData);
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
                        var subTotal = $(item);
                        var subTotal = $(item).attr('data-sub-total');
                        total += Number.parseFloat($(item).attr('data-sub-total'), 10);
                    }
                });
                if (total > 0) {
                    $('#total').html('Total Amount: ' + formatCurrency(total, unit));
                } else {
                    $('#total').html('Total Amount: 0');
                }
                $('[name="total_amount"]').val(total);
                @this.set('invoiceForm.total_amount', total);
            }

            function formatTime(time) {
                if (time >= 60) {
                    minutes = time % 60;
                    if (minutes > 0) {
                        return (time - minutes) / 60 + ' hrs ' + minutes + ' mins';
                    }
                    return time / 60 + ' hrs';
                }
                return time + ' mins';
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
