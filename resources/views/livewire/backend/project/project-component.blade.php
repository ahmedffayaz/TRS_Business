<div>
    @section('breadcrumbs', Breadcrumbs::render('projects'))
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Projects</h4>
            @can('add_projects')
                <div>
                    <x-anchor-tag href="#" class="btn btn-primary" tabindex="0" aria-controls="table-hover"
                        type="button" wire:click="openMainModal" value="Add Project" />
                </div>
            @endcan
        </div>
        <div class="card-body">
            @php
                $dataCount = [
                    'total' => $totalProjects,
                    'active' => $activeProjects,
                    'archived' => $archivedProjects
                ];
            @endphp

            <x-table-search :dataCounter="$dataCount" />

            <div class="table-responsive overflow-visible">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Budget</th>
                            <th>Members</th>
                            <th>Tasks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <x-anchor-tag href="{{ route('dashboard.projects.detail', $project->slug) }}" class="user_name text-truncate text-body">
                                            <span class="fw-bolder">{{ $project?->name }}</span>
                                        </x-anchor-tag>
                                        <small class="emp_post text-muted"><strong>Client:
                                            </strong>{{ $project?->client?->name }}</small>
                                    </div>
                                </td>
                                <td>{{ formatDate($project?->start_date) }}</td>
                                <td>{{ formatDate($project?->end_date) }}</td>
                                <td>
                                    <span
                                        class="badge rounded-pill badge-light-{{ status($project?->status) }}">{{ ucfirst(str_replace('-', ' ', $project?->status?->value)) }}</span>
                                </td>
                                <td>{{ $project?->budget }}</td>
                                <td>
                                    <span class="badge rounded-pill badge-light-primary me-1">{{ $project?->members_count }}</span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill badge-light-primary me-1">{{ $project?->tasks_count }}</span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        @can('edit_projects', 'delete_projects')
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_projects')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="edit({{ $project->id }})">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Edit</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('delete_projects')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="deleteConfirmation({{ $project->id }})">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @can('add_projects')
            <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal">
                @include('livewire.backend.project.form')
            </x-main-modal>
        @endcan
    </div>

</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });
        });
    </script>
@endscript
