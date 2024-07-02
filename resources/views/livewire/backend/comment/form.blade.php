<div>
    @php
        $minutes = \App\Enums\Comment\CommentUnit::MINUTES->value;
        $hours = \App\Enums\Comment\CommentUnit::HOURS->value;
    @endphp
    <div class="text-center mb-2">
        <h1 class="mb-1">{{ $form->isUpdate ? 'Edit' : 'Add' }} Task Time</h1>
    </div>
    <form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
        <div class="row mb-1">
            <div class="col-md-12">
                <span wire:ignore.>
                    <x-input-label for="comment" class="required" value="Comments" />
                    <x-textarea name="description" id="comment" rows="4" wire:model="form.description"
                    :class="$errors->has('form.description') ? 'error' : ''" />
                </span>
                @error('form.description')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <span wire:ignore.>
                    <x-input-label for="time" class="required" value="time" />
                    <x-input type="text" name="time" id="time"
                        :class="$errors->has('form.time') ? 'error' : ''"
                        placeholder="Enter time" wire:model="form.time" />
                </span>
                @error('form.time')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-6">
                <span>
                    <x-input-label for="unit-select" class="required" value="Unit" />
                    <x-select-input id="unit-select"
                        :class="$errors->has('form.unit') ? 'error select2' : 'select2'" wire:model="form.unit">
                        <option value="">--Select Unit--</option>
                        <option value="{{ $minutes }}">{{ $minutes }}</option>
                        <option value="{{ $hours }}">{{ $hours }}</option>
                    </x-select-input>
                </span>
                @error('form.unit')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="dated" class="required" value="Date" />
                <x-input type="text" name="dated" id="dated" placeholder="2020-09-23"
                    :class="$errors->has('form.dated') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="form.dated" autocomplete="off" />
                @error('form.dated')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="reports" value="Is Billable" />
                <div class="demo-inline-spacing">
                    <x-input-checkbox type="checkbox" id="is-billable" name="is_billable" wire:model="form.is_billable"
                        statusClass="form-check-success" :labelValue="__('Is Billable')" />
                    @error('form.is_billable')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
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
