<div class="card">
    <div class="card-header">
        <h4 class="card-title">Permissions</h4>
        <div>
            <a href="javascript:void(0);" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button"
                wire:click="openOffcanvas">Add Permission</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-4 col-sm-12">
                <div class="input-group input-group-merge">
                    <span class="input-group-text" id="basic-addon-search2" wire:ignore><i data-feather="search"></i></span>
                    <input type="text" class="form-control" wire:model.live.debounce.500ms="search"
                        placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search2" />
                </div>
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
        <div class="table-responsive overflow-visible">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Country</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($permissions)
                        @foreach ($permissions as $permission)
                            <tr>
                                <td>{{ $company?->name }}</td>
                                <td>
                                    {{ $company?->country?->name }}
                                </td>
                                <td><span
                                        class="badge rounded-pill badge-light-primary me-1">{{ $company?->employees_count }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                            data-bs-toggle="dropdown">
                                            <span wire:ignore><i data-feather="more-vertical"></i></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                wire:click="edit('{{ $company?->id }}')">
                                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                <span>Edit</span>
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                wire:click="deleteConfirmation('{{ $company?->id }}')">
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
            </table>
            {{-- {{ $permissions->links('components.pagination') }} --}}
        </div>
    </div>
</div>
