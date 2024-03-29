<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tasks</h4>
            <div>
                @can('add_invoices')
                    <x-anchor-tag href="#" class="btn btn-primary me-1 add-invoice" tabindex="0" aria-controls="table-hover"
                        type="button" value="Create Invoice" />
                @endcan
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
                    'archived' => $totalArchivedTasks
                ];
            @endphp

            <x-table-search :dataCounter="$dataCount" />

            <div class="card-table table-responsive card-min-height">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if (!$projectId)
                                <th>Project</th>
                            @else
                                <th></th>
                                <th>ID</th>
                            @endif
                            <th>p</th>
                            <th>Title</th>
                            <th>Deadline</th>
                            <th>Assigned To</th>
                            <th>Time Spent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                @if (!$projectId)
                                    <td>{{ $task?->project?->name }}</td>
                                @else
                                    <td>
                                        @if (count($task?->billableComments) > 0)
                                            <x-input-checkbox type="checkbox" id="daily-reports_{{ $task?->id }}" name="tasks[]"
                                                :value="$task?->id" statusClass="form-check-success" :labelValue="__('')" />
                                        @endif
                                    </td>
                                    <td>{{ $task?->id }}</td>
                                @endif
                                <td><span wire:ignore>{!! priorityToIcon($task?->priority) !!}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if (is_null($task?->deleted_at))
                                            <x-anchor-tag href="{{ route('dashboard.tasks.view', $task->id) }}" class="user_name text-truncate text-body">
                                                <span class="fw-bolder">{{ $task?->name }}</span>
                                            </x-anchor-tag>
                                        @else
                                            <span class="fw-bolder">{{ $task?->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ formatDate($task?->end_date) }}</td>
                                <td>
                                    <div class="design-group">
                                        <div data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom"
                                            title="{{ $task?->user?->fullName }}" class="avatar bg-light-{{ randomColors() }} pull-up">
                                            @if ($task?->user?->avatar)
                                                <img src="{{ getUserAvatar($task?->user) }}" alt="Avatar" width="33" height="33" />
                                            @else
                                                <div class="avatar-content">{{ $task?->user?->avatarName }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ formatTime($task?->comment?->sum('time')) }}</td>
                                <td>
                                    @if ($task?->completed_at)
                                    <span wire:ignore><i data-feather="check-square" class="text-success"></i></span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        @if ((auth()->user()->hasPermissionTo('edit_tasks') || auth()->user()->hasPermissionTo('delete_tasks')
                                            || auth()->user()->hasPermissionTo('view_tasks') || auth()->user()->hasPermissionTo('add_comments')
                                            || auth()->user()->hasPermissionTo('mark_completed')) && is_null($task?->deleted_at))
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="edit({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Edit</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('view_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="{{ route('dashboard.tasks.view', $task?->id) }}">
                                                        <span wire:ignore><i data-feather="eye" class="me-50"></i></span>
                                                        <span>View</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('add_comments')
                                                    <x-anchor-tag class="dropdown-item" href="#">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Comment</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('mark_completed')
                                                    <x-anchor-tag class="dropdown-item" href="#" wire:click="markComplete({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Mark Complete</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('delete_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="archiveConfirmation({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                                                        <span>Archive</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @if (auth()->user()->hasPermissionTo('delete_tasks') && empty($task?->user_id) && empty($task?->completed_at))
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="deleteConfirmation({{ $task?->id }})">
                                                        <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
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
                        @endforeach
                    </tbody>
                </table>
                {{ $tasks->links('components.pagination') }}
            </div>
        </div>
    </div>

    @if ($isTaskModalOpen)
        @can('add_tasks')
            <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal">
                @include('livewire.backend.task.form')
            </x-main-modal>
        @endcan
    @elseif($isAddInvoiceModalOpen)
        @can('add_invoices')
            <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeModal">
                @include('livewire.backend.invoice.form')
            </x-main-modal>
        @endcan
    @else
        <x-main-modal wireIgnoreSelf="wire:ignore.self">
        </x-main-modal>
    @endif
</div>

@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });

            $(document).on('click', '.add-invoice', function (event) {
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

                Livewire.dispatch('open-invoice-modal', {'tasks' : tasks});
                window.Swal.close();
            });
        });
    </script>
@endscript
