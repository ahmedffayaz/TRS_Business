<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tasks</h4>
            @can('add_tasks')
                <div>
                    <x-anchor-tag href="#" class="btn btn-primary" tabindex="0" aria-controls="table-hover"
                        type="button" wire:click="openMainModal" value="Add Task" />
                </div>
            @endcan
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

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            @if (!request()->routeIs('dashboard.projects.detail', $projectId))
                                <th>Project</th>
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
                                @if (!request()->routeIs('dashboard.projects.detail', $projectId))
                                    <td>{{ $task?->project?->name }}</td>
                                @endif
                                <td>{!! priorityToIcon($task?->priority) !!}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <x-anchor-tag href="#" class="user_name text-truncate text-body">
                                            <span class="fw-bolder">{{ $task?->name }}</span>
                                        </x-anchor-tag>
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
                                <td>Time Spent</td>
                                <td>
                                    @if ($task?->completed_at)
                                        <i data-feather="check-square" class="text-success"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        @can('edit_tasks', 'delete_tasks')
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown">
                                                <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="edit({{ $task->id }})">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Edit</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('view_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#">
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
                                                    <x-anchor-tag class="dropdown-item" href="#">
                                                        <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                        <span>Mark Complete</span>
                                                    </x-anchor-tag>
                                                @endcan
                                                @can('delete_tasks')
                                                    <x-anchor-tag class="dropdown-item" href="#"
                                                        wire:click="deleteConfirmation({{ $task->id }})">
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
                {{ $tasks->links('components.pagination') }}
            </div>
        </div>
    </div>
</div>
