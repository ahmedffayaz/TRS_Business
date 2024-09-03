<div>
    @section('breadcrumbs', Breadcrumbs::render('terms_conditions'))
    <div class="card">
        <div class="card-body  py-1 my-25">
            <div class="row mb-2">
                <div class="col-md-12 col-sm-6 text-end">
                    @can('add_terms_conditions')
                        <x-anchor-tag href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover"
                            type="button" wire:click="openMainModal">Add Terms & Conditions</x-anchor-tag>
                    @endcan
                </div>
            </div>
            <div class="container-fluid ms-1">
                <div class="row mb-2 d-flex justify-content-end align-items-center">
                    <div class="col-md-4 col-sm-12">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" wire:ignore id="basic-addon-search2">
                                <i data-feather="search"></i>
                            </span>
                            <input type="text" class="form-control" wire:model.live.debounce.500ms="search"
                                placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search2" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-datatable table-responsive" style="min-height: 280px;">
                <table class="datatables-permissions table">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Version</th>
                            <th>Title</th>
                            <th>Signed Date</th>
                            <th>Actions</th>
                        </tr>
                    <tbody>
                        @isset($termsConditions)
                            @can('view_terms_conditions')
                                @forelse ($termsConditions as $termsCondition)
                                    <tr>
                                        <td>{{ $termsCondition->id }}</td>
                                        <td>
                                            {{ $termsCondition->version }}
                                        </td>
                                        <td>
                                            {{ $termsCondition->title }}
                                        </td>
                                        <td>
                                            {{ $termsCondition->created_at }}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                @can('edit_terms_conditions', 'delete_terms_conditions')
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                        <span wire:ignore><i data-feather="more-vertical"></i></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @can('edit_terms_conditions')
                                                            <x-anchor-tag class="dropdown-item edit" href="javascript:void(0);" wire:click="edit('{{ $termsCondition?->id }}')">
                                                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                                <span>Edit</span>
                                                            </x-anchor-tag>
                                                        @endcan
                                                        @can('delete_terms_conditions')
                                                            <x-anchor-tag class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $termsCondition?->id }}')">
                                                                <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                                <span>Delete</span>
                                                            </x-anchor-tag>
                                                        @endcan
                                                    </div>
                                                @else
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <span wire:ignore.>
                                                            <i data-feather='lock'></i>
                                                        </span>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="no-hover">
                                        <td colspan="8" class="text-center py-1 fw-bold">
                                            <p>No Terms & Conditions Found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            @endcan
                        @endisset
                    </tbody>
                    </thead>
                </table>
                {{ $termsConditions->links('components.pagination') }}
            </div>
        </div>
    </div>
    <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal" modalTitle="{{ $form->isUpdate ? 'Edit' : 'Add' }} Terms & Conditions" buttonLabel="{{ $form->isUpdate ? __('Update') : __('Add') }}" formSubmit="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}" buttonStatus="true">
        <div class="row">
            <div class="col-md-12">
                    <div class="row mb-2">
                        <div class="col-md-6 mb-2">
                            <x-input-label for="title" class="required" :value="__('Title')" />
                            <x-input type="text" :class="$errors->has('form.title') ? 'form-control error' : 'form-control'" placeholder="Title"
                                data-msg="Please enter title" autofocus wire:model="form.title" />
                            @error('form.title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <x-input-label for="version" class="required" :value="__('Version')" />
                            <x-input type="number" :class="$errors->has('form.version') ? 'form-control error' : 'form-control'" placeholder="Version"
                                data-msg="Please enter version" min="1" step="0.1" autofocus wire:model="form.version" />
                            @error('form.version')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-1">
                            <span wire:ignore.>
                                <x-input-label for="roles" class="required" value="Roles" />
                                <x-select-input name="roles" id="role-select" multiple
                                    :class="$errors->has('form.roles') ? 'error select2' : 'select2'"
                                    wire:model="form.roles">
                                    @isset($roles)
                                        <option value="" selected disabled>--Select Role--</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role?->id }}">{{ $role?->title }}</option>
                                        @endforeach
                                    @endisset
                                </x-select-input>
                            </span>
                            @error('form.roles')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-12 mb-5">
                            <span wire:ignore.>
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
                            </span>
                            @error('form.description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
            </div>
        </div>
    </x-main-modal>
</div>

@script
    <script>
        $(document).ready(function() {
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });

            Livewire.dispatch('select-container');
            Livewire.on('roles-select', (data) => {
                var $select = $('#role-select');
                // Clear existing selections
                $select.val(null).trigger('change');
                // Set the new selections
                $select.val(data[0].formRoles).trigger('change');
            });

            $('#role-select').on('change', function(e) {
                @this.set('form.roles', $(this).val());
            });

            var $textareaName = "description"
            Livewire.dispatch('initialize-markup-editor', {'textarea': $textareaName});
            $(document).on('change keyup', '[name="description"]', function() {
                let $description = $(this).val();
                Livewire.dispatch('markup-editor-change', {'textarea' : $textareaName, 'value' : $description});
            });
        });
    </script>
@endscript
