<div>
    <div class="text-center mb-2">
        <h1 class="mb-1">{{ $form->isUpdate ? 'Edit' : 'Add' }} Project</h1>
    </div>
    <form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
        <div class="row mb-1">
            <div class="col-md-6">
                <span wire:ignore.>
                    <x-input-label for="client" class="required" value="Client" />
                    <x-select-input id="client-select" wire:model="form.client_id"
                        :class="$errors->has('form.client_id') ? 'error select2' : 'select2'">
                        @isset($clients)
                            <option value="">--Select Client--</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client?->id }}">{{ $client?->name }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                </span>
                @error('form.client_id')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div class="col-md-6">
                <x-input-label for="name" class="required" value="Name" />
                <x-input type="text" name="name" id="name" :class="$errors->has('form.name') ? 'error' : ''"
                    placeholder="Enter project name" wire:model="form.name" />
                @error('form.name')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-md-12 mb-2">
                <span wire:ignore.>
                    <x-input-label for="description" :value="__('Description')" />
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
                                @this.set('form.description', data)
                            });
                        "
                        wire:model.debounce.2000ms="form.description"
                    >{!! $form->description !!}</div>
                </span>
                @error('form.description')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <x-input-label for="start-date" class="required" value="Start Date" />
                <x-input type="text" name="start_date" id="start-date" placeholder="October 14, 2020"
                    :class="$errors->has('form.start_date') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="form.start_date" autocomplete="off" />
                @error('form.start_date')
                    <x-input-error :message="$message" />
                @enderror
            </div>


            <div class="col-md-6">
                <x-input-label for="end-date" value="End Date" />
                <x-input type="text" name="end_date" id="end-date" placeholder="October 14, 2020"
                    :class="$errors->has('form.end_date') ? 'error flatpickr-basic' : 'flatpickr-basic'"
                    wire:model="form.end_date" autocomplete="off" />
                @error('form.end_date')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                <x-input-label for="fixed-price" class="required" value="Type" />
                <x-input-radio labelName="Fixed Price" name="typ" id="fixed-price" value="fixed" :isChecked=false
                    wireModel="form.type" />
                @error('form.type')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div class="col-md-6">
                <x-input-label for="hours-basis" />
                <x-input-radio labelName="Hourly Basis" name="type" id="hourly-basis" value="hourly" :isChecked=false
                    wireModel="form.type" />
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-4">
                <x-input-label for="monthly-basis" class="required" value="Project Nature" />
                <x-input-radio labelName="Monthly Basis" name="nature" id="monthly-basis" value="monthly" :isChecked=false
                    wireModel="form.nature" />
                @error('form.nature')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div class="col-md-4">
                <x-input-label for="fixed" />
                <x-input-radio labelName="Fixed" name="nature" id="fixed" value="fixed" :isChecked=false
                    wireModel="form.nature" />
            </div>

            <div class="col-md-4">
                <x-input-label for="weekly-basis" />
                <x-input-radio labelName="Weekly Basis" name="nature" id="weekly-basis" value="weekly" :isChecked=false
                    wireModel="form.nature" />
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-4">
                <x-input-label for="budget" class="required" value="Budget" />
                <x-input type="text" name="budget" id="budget" placeholder="Enter budget"
                    :class="$errors->has('form.budget') ? 'error' : ''" wire:model="form.budget" />
                @error('form.budget')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-4">
                <x-input-label for="hourly-rate" value="Hourly Rate" />
                <x-input type="text" name="hourly_rate" id="hourly-rate" placeholder="Enter hourly rate"
                    :class="$errors->has('form.hourly_rate') ? 'error' : ''" wire:model="form.hourly_rate" />
                @error('form.hourly_rate')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-4">
                <span wire:ignore.>
                    <x-input-label for="currency" />
                    <x-select-input id="currency-select" wire:model="form.currency"
                        :class="$errors->has('form.currency') ? 'error select2' : 'select2'">
                        <option value="">--Select Currency--</option>
                        @foreach (currencies() as $name => $symbol)
                            <option value="{{ $name }}">{{ $symbol }}</option>
                        @endforeach
                    </x-select-input>
                </span>
                @error('form.currency')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-6">
                @php
                    $pendingStatus = App\Enums\Project\ProjectStatus::PENDING->value;
                    $inprogressStatus = App\Enums\Project\ProjectStatus::INPROGRESS->value;
                    $deliveredStatus = App\Enums\Project\ProjectStatus::DELIVERED->value;
                @endphp
                <span wire:ignore.>
                    <x-input-label for="select-project-status" class="required" value="Project Status" />
                    <x-select-input id="select-project-status" wire:model="form.status"
                        :class="$errors->has('form.status') ? 'error select2' : 'select2'">
                        <option value="">--Select Status--</option>
                        <option value="{{ $pendingStatus }}">{{ ucfirst($pendingStatus) }}</option>
                        <option value="{{ $inprogressStatus }}">{{ ucfirst($inprogressStatus) }}</option>
                        <option value="{{ $deliveredStatus }}">{{ ucfirst($deliveredStatus) }}</option>
                    </x-select-input>
                </span>
                @error('form.status')
                    <x-input-error :message="$message" />
                @enderror
            </div>

            <div class="col-md-6">
                @php
                    $archive = App\Enums\Project\ProjectIsAutoArchived::ARCHIVE->value;
                    $unarchive = App\Enums\Project\ProjectIsAutoArchived::UNARCHIVE->value;
                @endphp
                <span wire:ignore.>
                    <x-input-label for="select-project-auto-archive-status" class="required" value="Project Auto Archive Status" />
                    <x-select-input id="select-project-auto-archive-status" wire:model="form.is_auto_archive"
                        :class="$errors->has('form.is_auto_archive') ? 'error select2' : 'select2'">
                        <option value="">--Select Auto Archive Status--</option>
                        <option value="{{ $archive }}">Enable</option>
                        <option value="{{ $unarchive }}">Disable</option>
                    </x-select-input>
                </span>
                @error('form.is_auto_archive')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="members" value="Assign Members" />
                <span wire:ignore.>
                    <x-select-input name="members" id="members"
                        :class="$errors->has('form.members') ? 'error select2' : 'select2'" multiple wire:model="form.members">
                        <option value="">--Select Members--</option>
                        @isset($members)
                            @foreach ($members as $member)
                                <option value="{{ $member?->id }}">{{ $member?->name }} - {{ implode(', ', $member->roles->pluck('title')->toArray()) }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                </span>
                @error('form.members')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="reports" class="required" value="Reports" />
                <div class="demo-inline-spacing">
                    <x-input-checkbox type="checkbox" id="daily-reports" name="reports_schedule[]" wire:model="form.reports_schedule"
                        :value="__('daily')" statusClass="form-check-success" :labelValue="__('Daily')" />

                    <x-input-checkbox type="checkbox" id="weekly-reports" name="reports_schedule[]" wire:model="form.reports_schedule"
                        :value="__('weekly')" statusClass="form-check-success" :labelValue="__('Weekly')" />

                    <x-input-checkbox type="checkbox" id="monthly-reports" name="reports_schedule[]" wire:model="form.reports_schedule"
                        :value="__('monthly')" statusClass="form-check-success" :labelValue="__('Monthly')" />
                    @error('form.reports_schedule')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-label for="reports" value="Attachments" />
                @error('form.attachments')
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
            // Reinitialize select2 on client dropdown
            Livewire.on('client-select', (data) => {
                var $select = $('#client-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formClient).trigger('change');
            });

            $('#client-select').on('change', function(event) {
                @this.set('form.client_id', $(this).val());
            });

            // Reinitialize select2 on currency dropdown
            Livewire.on('currency-select', (data) => {
                var $select = $('#currency-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formCurrency).trigger('change');
            });

            $('#currency-select').on('change', function(event) {
                @this.set('form.currency', $(this).val());
            });

            // Reinitialize select2 on project status dropdown
            Livewire.on('project-status-select', (data) => {
                var $select = $('#select-project-status');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formStatus).trigger('change');
            });

            $('#select-project-status').on('change', function(event) {
                @this.set('form.status', $(this).val());
            });

            // Reinitialize select2 on project auto archive status dropdown
            Livewire.on('project-auto-archive-status-select', (data) => {
                var $select = $('#select-project-auto-archive-status');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formIsAutoArchived).trigger('change');
            });

            $('#select-project-auto-archive-status').on('change', function(event) {
                @this.set('form.is_auto_archive', $(this).val());
            });

            // Reinitialize select2 on project members dropdown
            Livewire.on('project-members-select', (data) => {
                var $select = $('#members');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formMembers).trigger('change');
            });

            $('#members').on('change', function(event) {
                @this.set('form.members', $(this).val());
            });

            Livewire.dispatch('flatpickr');
        })
    </script>
@endscript
