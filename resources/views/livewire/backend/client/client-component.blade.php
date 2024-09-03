@section('breadcrumbs', Breadcrumbs::render('clients'))
<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Clients</h4>
            <div class="d-flex justify-content-center gap-1">
            @can('add_clients')
                <div>
                    <x-anchor-tag href="{{ route('dashboard.clients.create') }}" class="btn btn-primary" tabindex="0"
                        aria-controls="table-hover" type="button">Add New Client</x-anchor-tag>
                </div>
            @endcan
            </div>
        </div>
        <div class="card-body">
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
                            <th>Employees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($clients)
                            @forelse ($clients as $client)
                                <tr>
                                    <td>
                                        <x-anchor-tag href="javascript:void(0)"
                                            wire:click="show('{{ $client?->slug }}')">{{ $client?->name }}</x-anchor-tag>
                                    </td>
                                    <td>
                                        {{ $client?->country?->name }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge rounded-pill badge-light-primary">{{ $client?->employees_count }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            @can('edit_clients', 'delete_clients')
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <span wire:ignore><i data-feather="more-vertical"></i></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('edit_clients')
                                                        <x-anchor-tag class="dropdown-item"
                                                            href="{{ route('dashboard.clients.edit', $client->slug) }}">
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
                            @empty
                                <tr class="no-hover">
                                    <td colspan="8" class="text-center py-1 fw-bold">
                                        <p>No Client Found</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endisset
                    </tbody>
                </table>
            </div>
            <div class="pagination-container d-flex justify-content-between align-items-center mt-2">
                <div class="d-flex align-items-center ms-2">
                    <span class="">Show</span>
                    <select class="form-select w-auto" wire:model.live.debounce.500ms="limitPerPage">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                        <option value="25">25</option>
                        <option value="30">30</option>
                        <option value="35">35</option>
                    </select>
                    <span class="">entries</span>
                </div>
                <div>
                    {{ $clients->links('components.pagination') }}
                </div>
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
        @if ($clientDetail?->employees)
            <hr>
            <div class="row">
                <div class="mb-2">
                    <h2 class="mb-1">Employees</h2>
                </div>
                @foreach ($clientDetail?->employees as $employee)
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-6">Name:</dt>
                            <dt class="col-sm-6">{{ $employee?->name }}</dt>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-4">Email:</dt>
                            <dt class="col-sm-4">{{ $employee?->email }}</dt>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <dl class="row">
                            <dt class="col-sm-4">Phone:</dt>
                            <dt class="col-sm-4">{{ $employee?->phone }}</dt>
                        </dl>
                    </div>
                @endforeach
            </div>
        @endif
    </x-main-modal>
</div>
@script
    <script type="module">
        $(document).ready(function() {

            Livewire.dispatch('feather-icons');
            Livewire.on('reinitialize-select-container', () => {
                $(document).ready(function() {
                    Livewire.dispatch('select-container');
                });
            });

             // Reinitialize icons
             Livewire.on('reinitialize-icons', () => {
                $(document).ready(function () {
                Livewire.dispatch('feather-icons');
                });
            });

        });
    </script>
@endscript
