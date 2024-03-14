@section('breadcrumbs', Breadcrumbs::render('clients'))
<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Clients</h4>
            @can('add_clients')
                <div>
                    <x-anchor-tag href="{{ route('dashboard.clients.create') }}" class="btn btn-primary"
                        tabindex="0" aria-controls="table-hover" type="button">Add Client</x-anchor-tag>
                </div>
            @endcan
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
                            <th>Employees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($clients)
                            @foreach ($clients as $client)
                                <tr>
                                    <td>
                                        <x-anchor-tag href="javascript:void(0)"
                                            wire:click="show('{{ $client?->slug }}')"
                                        >{{ $client?->name }}</x-anchor-tag>
                                    </td>
                                    <td>
                                        {{ $client?->country?->name }}
                                    </td>
                                    <td>
                                        {{ $client?->business?->name }}
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill badge-light-primary">{{ $client?->employees_count }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            @can('edit_clients', 'delete_clients')
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown">
                                                    <span wire:ignore><i data-feather="more-vertical"></i></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('edit_clients')
                                                        <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.clients.edit', $client->slug) }}">
                                                            <span wire:ignore>
                                                                <i data-feather="edit-2" class="me-50"></i>
                                                            </span>
                                                            <span>Edit</span>
                                                        </x-anchor-tag>
                                                    @endcan
                                                    @can('delete_clients')
                                                        <x-anchor-tag class="dropdown-item" href="javascript:void(0);"
                                                            wire:click="deleteConfirmation('{{ $client?->id }}')">
                                                            <span wire:ignore>
                                                                <i data-feather="trash" class="me-50"></i>
                                                            </span>
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
                            @endforeach
                        @endisset
                    </tbody>
                </table>
                {{ $clients->links('components.pagination') }}
            </div>
        </div>
    </div>

    <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeMainModal">
        <div class="mb-2">
            <h1 class="mb-1">{{ $clientDetail?->name . ' Details' }}</h1>
        </div>
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Business:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->business?->name }}</dd>
                </dl>

                <dl class="row">
                    <dt class="col-sm-4">Client:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->name }}</dd>
                </dl>

                <dl class="row">
                    <dt class="col-sm-4">Address:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->address }}</dd>
                </dl>

                <dl class="row">
                    <dt class="col-sm-4">City:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->city }}</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Country:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->country?->name }}</dd>
                </dl>

                <dl class="row">
                    <dt class="col-sm-4">Postal Code:</dt>
                    <dd class="col-sm-8">{{ $clientDetail?->postal_code }}</dd>
                </dl>

                <dl class="row">
                    <dt class="col-sm-6">Rate Per Hour:</dt>
                    <dd class="col-sm-6">{{ $clientDetail?->rate_per_hour }}</dd>
                </dl>
                <dl class="row">
                    <dt class="col-sm-6">Rate Per Hour Unit:</dt>
                    <dd class="col-sm-6">{{ $clientDetail?->rate_unit }}</dd>
                </dl>
            </div>
        </div>
    </x-main-modal>
</div>
