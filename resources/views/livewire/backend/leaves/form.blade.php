<div>
    @php
        $leave = \App\Enums\Leave\LeaveType::LEAVE->value;
        $halfLeave = \App\Enums\Leave\LeaveType::HALF_LEAV->value;
        $workFromHome = \App\Enums\Leave\LeaveType::WORK_FROM_HOME->value;
    @endphp
    <div class="text-center mb-2">
        <h1 class="mb-1">{{ $form->isUpdate ? 'Edit' : 'Add' }} Leave</h1>
    </div>
    <form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'createLeave' }}">
        <div class="row mb-1">
            <div class="col-md-4">
                <x-input-label for="start-date" class="required" value="Start Date" />
                <x-input type="text" name="start_date" id="start_date" placeholder="2020-09-23"
                    :class="$errors->has('form.start_date') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="form.start_date" autocomplete="off" />
                @error('form.start_date')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-4">
                <x-input-label for="end-date" class="required" value="End Date" />
                <x-input type="text" name="end_date" id="end_date" placeholder="2020-09-23"
                    :class="$errors->has('form.end_date') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="form.end_date" autocomplete="off" />
                @error('form.end_date')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-4">
                <span>
                    <x-input-label for="is-working" class="required" value="Is Working" />
                    <x-select-input id="is-working"
                    :class="$errors->has('form.is_working') ? 'error select2' : 'select2'" wire:model="form.is_working">
                    <option value="">--Select Leave Type--</option>
                    <option value="{{ $leave }}">{{ $leave }}</option>
                    <option value="{{ $halfLeave }}">{{ $halfLeave }}</option>
                    <option value="{{ $workFromHome }}">{{ $workFromHome }}</option>
                </x-select-input>
            </span>
            @error('form.is_working')
            <x-input-error :message="$message" />
            @enderror
        </div>
        </div>
    <div class="row mb-1">
        <div class="col-md-12">
            <x-input-label for="reason" value="Reason" />
            <x-textarea id="reason" data-length="200" length="200" rows="3"
                :class="$errors->has('form.reason') ? 'error char-textarea' : 'char-textarea'"
                placeholder="Enter Reason" wire:model="form.reason"/>
            @error('form.reason')
                <x-input-error :message="$message" />
            @enderror
        </div>
    </div>

        <div class="row">
            <div class="col-md-12 text-center">
                <x-button class="btn-primary me-1" type="submit" tabindex="4"
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
            // Reinitialize select2 on uni dropdown
            Livewire.on('unit-select', (data) => {
                var $select = $('#unit-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formUnit).trigger('change');
            });

            $('#unit-select').on('change', function(event) {
                @this.set('form.unit', $(this).val());
            });

            // Reinitialize flatpickr
            Livewire.dispatch('flatpickr');
        });
    </script>
@endscript
