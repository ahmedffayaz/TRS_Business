<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Clients</h4>
            <div>
                <a href="{{ route('dashboard.clients.create') }}" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button">Add Client</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4 col-sm-12">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text" wire:ignore id="basic-addon-search2"><i data-feather="search"></i></span>
                        <input type="text" class="form-control" wire:model.live.debounce.500ms="search" placeholder="Search..." aria-label="Search..."
                            aria-describedby="basic-addon-search2" />
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <h4 class="alert-heading d-flex align-items-center">
                        <span wire:ignore><i data-feather="alert-triangle" class="me-50"></i></span>
                        Success
                    </h4>
                    <div class="alert-body">
                        {{ session('success') }}
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
                            <th>Under Business</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($clients)
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client?->name }}</td>
                                    <td>
                                        {{ $client?->country?->name }}
                                    </td>
                                    <td>
                                        {{ $client?->business?->name }}
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical"></i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('dashboard.clients.edit', $client->slug) }}">
                                                    <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                    <span>Edit</span>
                                                </a>
                                                <a class="dropdown-item" href="javascript:void(0);" wire:click="deleteConfirmation('{{ $client?->id }}')">
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
                {{ $clients->links('components.pagination') }}
            </div>
        </div>
    </div>
</div>
