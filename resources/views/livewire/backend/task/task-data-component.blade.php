@push('styles')
    <style>
        .comment-icon {
            float: left;
            margin-right: 8px;
            height: 25px;
        }
    </style>
@endpush
<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tasks</h4>
            <div>
                @can('add_tasks')
                    <x-anchor-tag href="#" class="btn btn-outline-secondary me-1" tabindex="0" type="button"
                        wire:click="generateTaskPdf" value="Export as PDF" id="export-pdf-button" wire:ignore />
                @endcan
                @if ($projectId && auth()->user()->can('add_invoices'))
                    <x-anchor-tag href="#" class="btn btn-primary me-1 add-invoice" tabindex="0"
                        aria-controls="table-hover" type="button" value="Create Invoice" />
                @endif
                @can('add_tasks')
                    <x-anchor-tag href="#" class="btn btn-primary" tabindex="0" aria-controls="table-hover"
                        type="button" wire:click="openModal" value="Add Task" />
                @endcan
            </div>
        </div>
        <div class="card-body">
            @php
                $dataCount = [
                    'total' => $totalTasks,
                    'active' => $totalActiveTasks,
                    'archived' => $totalArchivedTasks,
                ];
            @endphp


            <div class="row mb-2 d-flex justify-content-between align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    <div>
                    <!-- Adjust column sizes and spacing -->
                    <div class="ms-1">
                        @role('admin')
                            <button class="btn btn-outline-primary" data-filter="close" id="filter-toggle" wire:ignore>
                                <i data-feather="filter"></i> Filter
                            </button>
                        @endrole
                    </div>
                </div>
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

            <div id="filter-area" class="d-none mb-2" wire:ignore.self>
                <div class="row">
                    @if ($is_taskComponent)
                        <div class="col-md-4 col-sm-12">
                            <x-select-input placeholder="Filter by project" wire:model.live="filterProject"
                                id="filterProject" class="select2">
                                <option value="">Select project</option>
                                @isset($projectsForFilter)
                                    @php
                                        $sortedProjects = $projectsForFilter
                                            ->unique('project_id')
                                            ->sortBy(function ($task) {
                                                return $task->project?->name;
                                            });
                                    @endphp
                                    @foreach ($sortedProjects as $project)
                                        <option value="{{ $project?->project?->id }}">
                                            {{ ucwords($project?->project?->name) }}</option>
                                    @endforeach
                                @endisset
                            </x-select-input>
                        </div>
                    @endif
                    <div class="col-md-3 col-sm-12">
                        <x-select-input placeholder="Filter by developer" wire:model.defer="developerId"
                            id="developerId" class="select2">
                            <option value="">Select developer</option>
                            @if ($developers)
                                @foreach ($developers as $developer)
                                    <option value="{{ $developer->id }}">{{ ucwords($developer->fullName) }}</option>
                                @endforeach
                            @else
                                @foreach ($tasks->unique('user_id') as $task)
                                    <option value="{{ $task?->user?->id }}">{{ ucwords($task->user->fullName) }}
                                    </option>
                                @endforeach
                            @endif
                        </x-select-input>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <x-input type="text" class="flatpickr-range" name="filterDate" id="filterDate"
                            placeholder="YYYY-MM-DD to YYYY-MM-DD" wire:model.defer="filterDate" />
                    </div>

                    <div class="col-md-2 col-sm-12 align-self-end">
                        <button class="btn btn-outline-primary" wire:ignore title="Apply filter" id="apply-filter"
                            wire:click="applyFilter($('#developerId').val(),$('#filterDate').val(),$('#filterProject').val())"
                            disabled>Apply</button>
                    </div>
                </div>

            </div>

            <div class="card-table table-responsive card-min-height" style="overflow-x: hidden;">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @role('admin')
                                @if ($is_taskComponent)
                                    <th style="padding: .72rem .7rem;">
                                        <x-input-checkbox type="checkbox" id="select-all-checkbox"
                                            statusClass="form-check-primary" :labelValue="__('')" />
                                    </th>
                                @endif
                                @if (!$projectId)
                                    {{-- <th>Project</th> --}}
                                @else
                                    <th></th>
                                @endif
                            @endrole
                            {{-- <th class="text-nowrap">p</th> --}}
                            <th class="text-nowrap" style="padding: .72rem .72rem;">Title</th>
                            <th class="text-nowrap">Deadline</th>
                            {{-- <th class="text-nowrap">Assigned To</th> --}}
                            <th class="text-nowrap">Time Spent</th>
                            <th class="text-nowrap">Status</th>
                            <th class="text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                @role('admin')
                                    @if ($is_taskComponent)
                                        <td style="padding: .72rem .7rem;">
                                            <x-input-checkbox type="checkbox" id="selected_projects{{ $task?->id }}"
                                                name="selected_projects[]" :value="$task?->id"
                                                statusClass="form-check-primary" :labelValue="__('')"
                                                wire:model="selectedProjects" class="project-checkbox" />
                                        </td>
                                    @endif
                                    @if (!$projectId)
                                        {{-- <td>{{ $task?->project?->name }}</td> --}}
                                    @else
                                        <td style="padding: .72rem .5rem;">
                                            @if (count($task?->billableComments) > 0)
                                                <x-input-checkbox type="checkbox" id="daily-reports_{{ $task?->id }}"
                                                    name="tasks[]" :value="$task?->id" statusClass="form-check-success"
                                                    :labelValue="__('')" wire:model="selectedTasks"
                                                    class="project-checkbox" />
                                            @endif
                                        </td>
                                    @endif
                                @endrole


                                <td class="text-nowrap" style="padding: .72rem .72rem;">
                                    <div class="d-flex align-items-center">
                                        <div data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                            data-bs-placement="bottom" title="{{ $task?->user?->fullName }}"
                                            class="avatar bg-light-{{ randomColors() }} pull-up me-2">
                                            @if ($task?->user?->avatar)
                                                <img src="{{ getUserAvatar($task?->user) }}" alt="Avatar"
                                                    width="33" height="33" />
                                            @else
                                                <div class="avatar-content">{{ $task?->user?->avatarName }}</div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="d-flex flex-row align-items-center">
                                                @if (is_null($task?->deleted_at))
                                                    <x-anchor-tag
                                                        href="{{ route('dashboard.tasks.view', $task->id) }}"
                                                        style="color: inherit;"
                                                        class="user_name text-truncate text-body fw-bolder">
                                                        <span class="fw-bolder">{{ ucwords($task?->name) }}</span>
                                                    </x-anchor-tag>

                                                    <span class="ms-1">{{ $task->comments->count() }}</span>
                                                    <img class="comment-icon"
                                                        src="{{ asset('assets/images/Comment icon.png') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="This task have {{ $task->comments->count() }} comments" />

                                                    <span wire:ignore data-bs-toggle="tooltip"
                                                        data-bs-placement="bottom"
                                                        title="Task priority: {{ ucwords($task?->priority) }}">
                                                        {!! priorityToIcon($task?->priority) !!}
                                                    </span>
                                                @else
                                                    <span class="fw-bolder">{{ $task?->name }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-row">
                                                <small class="emp_post text-muted"><strong>Project:</strong>
                                                    {{ $task?->project?->name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </td>


                                <td class="text-nowrap">{{ formatDate($task?->end_date) }}</td>

                                <td class="text-nowrap">{{ formatTime($task?->comments?->sum('time')) }}</td>
                                <td class="text-nowrap">
                                    @if ($task?->completed_at)
                                        <span class='badge badge-light-success text-capitalize'
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Task Completed">Completed</span>
                                    @else
                                        <span class='badge badge-light-warning text-capitalize'
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Task Incompleted">Incomplete</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <div class="dropdown">
                                        @if (
                                            (auth()->user()->hasPermissionTo('edit_tasks') ||
                                                auth()->user()->hasPermissionTo('delete_tasks') ||
                                                auth()->user()->hasPermissionTo('view_tasks') ||
                                                auth()->user()->hasPermissionTo('mark_completed')) &&
                                                is_null($task?->deleted_at))
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="edit({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="edit-2"
                                                                class="me-50"></i></span>
                                                        <span>Edit</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('view_tasks')
                                                    <x-anchor-tag class="dropdown-item"
                                                        href="{{ route('dashboard.tasks.view', $task?->id) }}">
                                                        <span wire:ignore><i data-feather="eye" class="me-50"></i></span>
                                                        <span>View</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('mark_completed')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="markComplete({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="edit-2"
                                                                class="me-50"></i></span>
                                                        <span>Mark Complete</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('delete_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="archiveConfirmation({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="trash"
                                                                class="me-50"></i></span>
                                                        <span>Archive</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @if (auth()->user()->hasPermissionTo('delete_tasks') && empty($task?->user_id) && empty($task?->completed_at))
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="deleteConfirmation({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="trash"
                                                                class="me-50"></i></span>
                                                        <span>Delete</span>
                                                    </x-anchor-tag>
                                                @endif
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore.>
                                                    <i data-feather='lock'></i>
                                                </span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="no-hover">
                                <td colspan="8" class="text-center py-1 fw-bold text-nowrap">
                                    <p>No Task Found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-container d-flex justify-content-between align-items-center mt-2">
                <x-table-pagination limitPerPage="limitPerPage"></x-table-pagination>
                <div>
                    {{ $tasks->links('components.pagination') }}
                </div>
            </div>
        </div>
    </div>

    @if ($isTaskModalOpen)
        @can('add_tasks')
            <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal" modalTitle="{{ $form->isUpdate ? 'Edit' : 'Add' }} Task" modalTitle="{{ $form->isUpdate ? 'Edit' : 'Add' }} User" buttonLabel="{{ $form->isUpdate ? 'Update' : 'Add' }}" formSubmit="{{ $form->isUpdate ? 'update(' . $form->id . ')' : 'store' }}" multipart="multipart/form-data" buttonStatus="true">
                @include('livewire.backend.task.form')
            </x-main-modal>
        @endcan
    @elseif($isAddInvoiceModalOpen)
        @can('add_invoices')
            <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeInvoiceModal" modalTitle="Create Invoice">
                @include('livewire.backend.invoice.form')
            </x-main-modal>
        @endcan
    @elseif ($isRevenueModalOpen)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="modal-sm" closeModal="closeRevenueModal">
            <div class="mb-2">
                <h1 class="mb-1">Project Revenue</h1>
            </div>
            <div class="row">
                <p>{!! $projectRevenue['projectRevenueDetail'] !!}</p>
            </div>
        </x-main-modal>
    @elseif ($isInviteClientModalOpen)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" modalTitle="Share link" modalSize="modal-default">
            @include('livewire.backend.project.invite-client')
        </x-main-modal>
    @else
        <x-main-modal wireIgnoreSelf="wire:ignore.self">
        </x-main-modal>
    @endif
</div>

@script
    <script type="module">
        $(document).ready(function() {
            Livewire.dispatch('flatpickr');
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                $(document).ready(function() {
                    Livewire.dispatch('feather-icons');
                    Livewire.dispatch('select-container');
                    Livewire.dispatch('flatpickr');

                });
            });

            $(document).on('click', '.add-invoice', function(event) {
                event.preventDefault();
                window.Swal.fire({
                    text: 'Please wait..',
                    showCancelButton: false,
                    showConfirmButton: false
                });

                const tasks = [];
                $.each($("input[name='tasks[]']:checked"), function() {
                    tasks.push($(this).val());
                });

                window.location.href = `/dashboard/invoices/create?tasks=${tasks}`;

                // Livewire.dispatch('open-invoice-modal', {'tasks' : tasks});
                window.Swal.close();
            });


            $('#filter-toggle').on('click', function() {
                $('#filter-area').toggleClass('d-none d-block');
                if ($(this).attr('data-filter') === 'open') {
                    $(this).attr('data-filter', 'close')
                        .html('<i data-feather="filter"></i> Filter');
                    Livewire.dispatch('reset-task-filter');
                }
            });

            $("#apply-filter").on('click', function() {
                $('#filter-toggle').attr('data-filter', 'open').html(
                    '<i data-feather="x"></i> Remove filter');
            });

            document.addEventListener('reset-task-filters', function() {
                $('#filterProject').val('').trigger('change');
                $('#developerId').val('').trigger('change');
                $('#filterDate').val('').trigger('change');
            });

            $(document).on('change', '#filterProject, #developerId, #filterDate', function() {

                checkDropdowns();
            });

            function checkDropdowns() {
                let projectSelected = $('#filterProject').val() !== '';
                let developerSelected = $('#developerId').val() !== '';
                let dateSelected = $('#filterDate').val() !== '';

                if (projectSelected || developerSelected || dateSelected) {
                    $('#apply-filter').prop('disabled', false);
                } else {
                    $('#apply-filter').prop('disabled', true);
                }
            }
            checkDropdowns();

            function toggleExportButton() {
                if ($('.project-checkbox:checked').length > 0) {
                    $('#export-pdf-button').show();
                } else {
                    $('#export-pdf-button').hide();
                }
            }

            // Attach the change event to the "Select All" checkbox
            $(document).on('change', '#select-all-checkbox', function() {
                const isChecked = $(this).is(':checked');
                var ids = [];

                $(document).find('.project-checkbox').each(function() {
                    $(this).prop('checked', isChecked); // Check/uncheck all checkboxes
                    if (isChecked) {
                        ids.push($(this).val()); // Add ID if "Select All" is checked
                    }
                });

                // Update the selectedProjects property in Livewire component
                @this.set('selectedProjects', isChecked ? ids : []);
                toggleExportButton();
            });

            // Attach the change event to individual checkboxes
            $(document).on('change', '.project-checkbox', function() {
                toggleExportButton();

                // If all checkboxes are checked, check "Select All" checkbox
                const allChecked = $('.project-checkbox').length === $('.project-checkbox:checked').length;
                $('#select-all-checkbox').prop('checked', allChecked);

                // Update the selectedProjects property in Livewire component
                var ids = [];
                $(document).find('.project-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                @this.set('selectedProjects', ids);

                toggleExportButton();
            });

            // Initial check on page load
            toggleExportButton();
        });
    </script>
@endscript
