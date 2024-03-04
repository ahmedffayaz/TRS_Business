@section('breadcrumbs', Breadcrumbs::render('permissions'))
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
                    <a href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button" wire:click="openMainModal">Add Permission</a>
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
            <div class="card-datatable table-responsive">
                <table class="datatables-permissions table">
                    <thead class="table-light">
                        <tr>
                            <th>Group</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    <tbody>
                        @isset($permissions)
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td>{{ $permission?->group }}</td>
                                    <td>
                                        {{ $permission?->title }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical"></i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="edit('{{ $permission?->id }}')">
                                                    <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                    <span>Edit</span>
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $permission?->id }}')">
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
                {{ $permissions->links('components.pagination') }}
            </div>
        </div>
    </div>
    <x-main-modal wireIgnoreSelf="wire:ignore.self">
        <div class="text-center mb-2">
            <h1 class="mb-1">{{ $form->isUpdate ? 'Update' : 'Add' }} Permission</h1>
            <p>Permissions you may use and assign to your users.</p>
        </div>
        <form class="row" wire:submit.prevent="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}">
            <div class="col-6">
                <label class="form-label" for="modalPermissionName"> Group</label>
                <input type="text" class="form-control  @error('form.group') is-invalid @enderror" wire:model="form.group" placeholder="Permission Group" autofocus data-msg="Please enter permission name" />
                @error('form.group')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-6">
                <label class="form-label" for="modalPermissionName"> Name</label>
                <input type="text" class="form-control  @error('form.title') is-invalid @enderror" wire:model="form.title" placeholder="Permission Name" autofocus data-msg="Please enter permission name" />
                @error('form.title')
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
