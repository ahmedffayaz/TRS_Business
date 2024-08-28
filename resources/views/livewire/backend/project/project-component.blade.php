<div>
    @section('breadcrumbs', Breadcrumbs::render('projects'))
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Projects</h4>
            @can('add_projects')
            <div>
                <x-anchor-tag href="#" class="btn btn-primary" tabindex="0" aria-controls="table-hover" type="button"
                    wire:click="openMainModal" value="Add Project" />
            </div>
            @endcan
        </div>
        <div class="card-body">
            @if (session()->has('status'))
                    <div class="alert alert-success p-1" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session()->has('error'))
                <div class="alert alert-error alert-danger p-1" x-data="{ show: true }" x-show="show"
                    x-init="setTimeout(() => show = false, 3000)">
                    {{ session('error') }}
                </div>
            @endif
            @php
            $dataCount = [
            'total' => $totalProjects,
            'active' => $activeProjects,
            'archived' => $archivedProjects
            ];
            @endphp

            <x-table-search :dataCounter="$dataCount" />

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Members</th>
                            <th>Tasks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                        <tr>
                            <td>
                                <div class="d-flex flex-column">
                                    <x-anchor-tag href="{{ route('dashboard.projects.detail', $project->slug) }}"
                                        class="user_name text-truncate text-body">
                                        <span class="fw-bolder text-capitalize">{{ $project?->name }}</span>
                                    </x-anchor-tag>
                                    <small class="emp_post text-muted"><strong>Client:
                                        </strong>{{ $project?->client?->name }}</small>
                                </div>
                            </td>
                            <td>{{ formatDate($project?->start_date) }}</td>
                            <td>{{ formatDate($project?->end_date) }}</td>
                            <td>
                                <span class="badge rounded-pill badge-light-{{ status($project?->status) }}">{{
                                    ucfirst(str_replace('-', ' ', $project?->status?->value)) }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill badge-light-primary me-1">{{ $project?->members_count
                                    }}</span>
                            </td>
                            <td>
                                <span class="badge rounded-pill badge-light-primary me-1">{{ $project?->tasks_count
                                    }}</span>
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
                                        @if(!$project->deleted_at)
                                        @can('delete_projects')
                                        <x-anchor-tag class="dropdown-item" href="#"
                                            wire:click="deleteConfirmation({{ $project->id }})">
                                            <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                            <span>Delete</span>
                                        </x-anchor-tag>
                                        @endcan
                                        @else
                                        @can('restore_projects')
                                        <x-anchor-tag class="dropdown-item" href="#"
                                            wire:click="restoreConfirmation({{ $project->id }})">
                                            <span wire:ignore><i data-feather="refresh-cw" class="me-50"></i></span>
                                            <span>Restore</span>
                                        </x-anchor-tag>
                                        @endcan
                                        @endif
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
                                <p>No Project Found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $projects->links('components.pagination') }}
            </div>
        </div>

        @can('add_projects')
        <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal" modalTitle="{{ $form->isUpdate ? 'Edit' : 'Add' }} Project">
            @include('livewire.backend.project.form')
        </x-main-modal>
        @endcan
    </div>

</div>

@script
@if($show_swl)
<script type="module">
  window.Swal.fire({
    title: 'Confirmation Required',
    text: 'Please confirm the invitation.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Accept',
    cancelButtonText: 'Reject',
  }).then((result) => {
    if (result.isConfirmed) {
        Livewire.dispatch('accept_invite_link')
    } else {
        Livewire.dispatch('reject_invite_link')
    }
  });
</script>
@endif
<script type="module">
    $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                $(document).ready(function () {
                Livewire.dispatch('feather-icons');
                });
            });
        });
</script>
@endscript
