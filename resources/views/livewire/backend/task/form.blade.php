<div>
    <form wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}" enctype="multipart/form-data">
        <div class="row mb-1">
            @if (!$projectId)
                <div class="col-md-6">
                    <span wire:ignore.>
                        <x-input-label for="select-project" class="required" value="Project" />
                        <x-select-input id="select-project" wire:model="form.project_id"
                            :class="$errors->has('form.project_id') ? 'error select2' : 'select2'">
                            @isset($projects)
                                <option value="">--Select Project--</option>
                                @foreach ($projects as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            @endisset
                        </x-select-input>
                    </span>
                    @error('form.project_id')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
            @endif
            <div class="col-md-{{ $projectId ? '12' : '6' }}">
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
            @php
                $lowPriority = App\Enums\Task\TaskPriority::LOW->value;
                $mediumPriority = App\Enums\Task\TaskPriority::MEDIUM->value;
                $highPriority = App\Enums\Task\TaskPriority::HIGH->value;
            @endphp
            <div class="col-md-6">
                <x-input-label for="low-priority" class="required" value="Priority" />
                <div class="row">
                    <div class="col-md-4">
                        <x-input-radio labelName="{{ ucfirst($lowPriority) }}" name="priority" id="low-priority"
                            value="{{ $lowPriority }}" :isChecked=false wireModel="form.priority" />
                    </div>
                    <div class="col-md-4">
                        <x-input-radio labelName="{{ ucfirst($mediumPriority) }}" name="priority" id="medium-priority"
                            value="{{ $mediumPriority }}" :isChecked=false wireModel="form.priority" />
                    </div>
                    <div class="col-md-4">
                        <x-input-radio labelName="{{ ucfirst($highPriority) }}" name="priority" id="high-priority"
                            value="{{ $highPriority }}" :isChecked=false wireModel="form.priority" />
                    </div>
                </div>
                @error('form.priority')
                    <x-input-error :message="$message" />
                @enderror
            </div>
            <div class="col-md-6">
                <span wire:ignore.>
                    <x-input-label for="assigned-member" class="required" value="Assigned Member" />
                    <x-select-input id="assigned-member" wire:model="form.user_id"
                        :class="$errors->has('form.user_id') ? 'error select2' : 'select2'">
                        @isset($members)
                            <option value="">--Select User--</option>
                            @foreach ($members as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        @endisset
                    </x-select-input>
                </span>
                @error('form.user_id')
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
            <div class="col-md-12">
                <livewire:dropzone
                wire:model="form.attachments"
                :rules="['mimes:png,jpeg,jpeg,pdf,doc,docx','max:10420']"
                :multiple="true"/>
                @error('form.attachments')
                    <x-input-error :message="$message" />
                @enderror
            </div>
        </div>
        @if(count($editableFiles) > 0 && $form->isUpdate == true )
        <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full dz-mt-5 mb-2">
            @foreach($editableFiles as $file)
            @if(array_key_exists('id', $file))
                <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full dz-h-auto dz-overflow-hidden dark:dz-border-gray-700">
                    <div class="dz-flex dz-items-center dz-gap-3">
                            <div class="dz-flex-none dz-w-14 dz-h-14">
                                <img src="{{ asset('storage/' . $file['file']) }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $file['name'] }}">
                            </div>
                        <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                            <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium dark:dz-text-slate-100">{{ $file['name']}}</div>
                            <div class="dz-text-center dz-text-gray-500 dz-text-sm dz-font-medium">{{ \Illuminate\Support\Number::fileSize($file['size']) }}
                            </div>
                        </div>
                    </div>
                    <div class="dz-flex dz-items-center dz-mr-3">
                        <button type="button" wire:click="removeFileConfirmation('{{ $file['uuid'] }}')" class="bg-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="dz-w-6 dz-h-6 dz-text-black dark:dz-text-white">
                                <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>

                    </div>
                </div>
            @endif
            @endforeach
        </div>
    @endif
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
            Livewire.dispatch('select-container');
            Livewire.on('resetSelectInput', () => {
                $(document).ready(function () {
                    Livewire.dispatch('select-container');
                });
            });
            // Reinitialize select2 on project dropdown
            Livewire.on('project-select', (data) => {
                var $select = $('#select-project');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formProject).trigger('change');
            });

            $('#select-project').on('change', function(event) {
                @this.set('form.project_id', $(this).val());
            });

            // Reinitialize select2 on assigned to dropdown
            Livewire.on('assigned-member-select', (data) => {
                var $select = $('#assigned-member');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formUser).trigger('change');
            });

            $(document).on('change', '#assigned-member', function(event) {
                @this.set('form.user_id', $(this).val());
            });

            // Reinitialize flatpickr
            Livewire.dispatch('flatpickr');
            Livewire.on('reinitialize-dispatcher', () => {
                $(document).ready(function () {
                    Livewire.dispatch('flatpickr');
                })
            })
        });
    </script>
@endscript
