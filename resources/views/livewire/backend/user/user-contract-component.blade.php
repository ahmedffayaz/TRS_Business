<div>
    @section('breadcrumbs', Breadcrumbs::render('user_contracts', $user))
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">My Contracts</h4>
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

            <div class="table-responsive overflow-visible">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Version</th>
                            <th>Title</th>
                            <th>Signed Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @isset($contracts)
                            @foreach ($contracts as $contract)
                                <tr>
                                    <td>{{ $contract?->id }}</td>
                                    <td>{{ $contract?->termsCondition?->version }}</td>
                                    <td>{{ $contract?->termsCondition?->title }}</td>
                                    <td>
                                        <span class="badge rounded-pill badge-light-primary">{{ formatDate($contract->created_at) }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            @can('view_contracts', 'download_contracts')
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                    <span wire:ignore><i data-feather="more-vertical"></i></span>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    @can('view_contracts')
                                                        <x-anchor-tag class="dropdown-item view-contract" target="_blank"
                                                            href="{{ route('dashboard.users.contracts.view', $contract->id) }}">
                                                            <span wire:ignore>
                                                                <i data-feather="eye" class="me-50"></i>
                                                            </span>
                                                            <span>View</span>
                                                        </x-anchor-tag>
                                                    @endcan
                                                    @can('download_contracts')
                                                        <x-anchor-tag class="dropdown-item" href="{{ asset($contract->pdf_url) }}"
                                                            download>
                                                            <span wire:ignore>
                                                                <i data-feather="download" class="me-50"></i>
                                                            </span>
                                                            <span>Download</span>
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
                {{ $contracts->links('components.pagination') }}
            </div>
        </div>
    </div>
</div>
