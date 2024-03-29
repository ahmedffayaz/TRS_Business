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
                        statusClass="form-check-inline" :labelValue="__('Send Emails')" />
                    @error('invoiceForm.isEmail')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-md-12">
                <x-input-checkbox type="checkbox" id="select-all-comments" name="all_comments" wire:model="invoiceForm.all_comments"
                    statusClass="form-check-inline" :labelValue="__('Select/De-select all comments ')" />
                @error('invoiceForm.all_comments')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>
        @if ($tasksList)
            @include('livewire.backend.invoice.tasks-list', ['billableTasks' => $tasksList])
        @endif
        <div class="row mb-1">
            <div class="col-md-12">
                <x-anchor-tag href="javascript:void(0);" class="btn btn-primary float-end" tabindex="0" aria-controls="table-hover"
                    type="button" value="Add Generic Comment" id="add-generic-comments-fields" wire:click="addGenericCommentsFields({{ $i }})" />
            </div>
        </div>

        @foreach($inputs as $key => $value)
            @include('livewire.backend.invoice.generic-comment-form', ['key' => $key])
        @endforeach

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
                <x-input-label for="deduction" value="Amount Adjustment" />
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
            Livewire.on('reinitialize-feather-icons', () => {
                $(document).ready(function () {
                    Livewire.dispatch('feather-icons');
                });
            });
        });
    </script>
@endscript
