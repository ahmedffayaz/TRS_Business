<div>
    <div class="card">
        <div class="card-body  py-1 my-25">
            <div class="row mb-2">
                <div class="col-md-4 col-sm-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" wire:ignore id="basic-addon-search2"><i data-feather="search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..." aria-label="Search..."
                            aria-describedby="basic-addon-search2" />
                    </div>
                </div>
                <div class="col-md-8 col-sm-6 text-end">
                    <a href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button" wire:click="openMainModal">Add Contract </a>
                </div>
            </div>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <h4 class="alert-heading d-flex align-items-center">
                        <span wire:ignore><i data-feather="alert-triangle" class="me-50"></i></span>
                        Error
                    </h4>
                    <div class="alert-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
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
                        @isset($contracts)
                            @foreach ($contracts as $contract)
                                <tr>
                                    <td>{{ $contract->id }}</td>
                                    <td>
                                        {{ $contract->version }}
                                    </td>
                                    <td>
                                        {{ $contract->title }}
                                    </td>
                                    <td>
                                        {{ $contract->created_at }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical"></i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="edit('{{ $contract?->id }}')">
                                                    <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                    <span>Edit</span>
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $contract?->id }}')">
                                                    <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                    <span>Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                    </tbody>
                    </thead>
                </table>
                {{ $contracts->links('components.pagination') }}
            </div>
        </div>
    </div>
    <x-main-modal wireIgnoreSelf="wire:ignore.self">
        <div class="text-center mb-2">
            <h1 class="mb-1">{{ $form->isUpdate ? 'Update' : 'Add' }} Contract </h1>
        </div>
        <form class="row" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <div class="col-6 mt-75">
                <label class="form-label"> Title</label>
                <input type="text" class="form-control  @error('form.title') is-invalid @enderror" wire:model="form.title" placeholder="Title" autofocus data-msg="Please enter title" />
                @error('form.title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-6 mt-75">
                <label class="form-label"> Version </label>
                <input type="text" class="form-control  @error('form.version') is-invalid @enderror" wire:model="form.version" placeholder="Version" autofocus data-msg="Please enter version" />
                @error('form.version')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-md-6  mt-75">
                <label class="col-form-label" for="Date Format">Role</label>
                <select name="roles[]" class="select2 form-select   @error('form.roles') is-invalid @enderror" id="select2-multiple" multiple wire:model="form.roles">
                    <option value="" selected disabled>--Select Role--</option>
                    @foreach($roles as $key => $role)
                        <option value="{{ $key  }}">{{ $role }}</option>
                    @endforeach
                </select>
                @error('form.roles')
                    <small class="text-danger mt-2">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-6 mt-75">
                <label class="form-label"> Description </label>
                <textarea row="3" class="form-control  @error('form.description') is-invalid @enderror" wire:model="form.description" placeholder="Description" autofocus data-msg="Please enter description"></textarea>
                @error('form.description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-12 text-end mt-75">
                <button class="btn btn-primary me-1 waves-effect waves-float waves-light" tabindex="4" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $form->isUpdate ? __('Update Changes') : __('Save Changes') }}</span>
                    <span wire:loading>
                        <i class="fa fa-spinner fa-spin " wire:ignore></i> {{ __('Loading...') }}
                    </span>
                </button>
            </div>
        </form>
    </x-main-modal>
</div>
