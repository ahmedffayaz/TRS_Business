<div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h5 class="mb-0">Billable</h5>
                        @can('add_task')
                            <div>
                                <x-anchor-tag href="#" class="btn btn-primary float-end" tabindex="0" aria-controls="table-hover"
                                    type="button" wire:click="openModal" value="Add Time" />
                            </div>
                        @endcan
                    </div>

                    {{-- table search section --}}
                    <x-table-search :dataCounter="[]" />

                    <div class="card-table table-responsive card-min-height">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Time</th>
                                    <th class="text-nowrap">Added By</th>
                                    <th class="text-nowrap">Dated At</th>
                                    <th class="text-nowrap">Invoiced At</th>
                                    <th class="text-nowrap">Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $comment)
                                    <tr>
                                        <td>{{ $comment?->id }}</td>
                                        <td>{{ $comment?->description }}</td>
                                        <td class="text-nowrap">{{ formatTime($comment?->time) }}</td>
                                        <td class="text-nowrap">
                                            <x-anchor-tag href="{{ route('dashboard.users.profile', $comment?->fromUser?->id) }}"
                                                :value="$comment?->fromUser?->fullName" />
                                        </td>
                                        <td class="text-nowrap">{{ formatDate($comment?->dated) }}</td>
                                        <td class="text-nowrap">{{ formatDate($comment?->invoiced_at) }}</td>
                                        <td class="text-nowrap">{{ formatDate($comment?->created_at) }}</td>
                                        <td>
                                            <div class="dropdown">
                                                @can('edit_comments', 'delete_comments')
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                        data-bs-toggle="dropdown">
                                                        <span wire:ignore><i data-feather="more-vertical">open</i></span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        @can('edit_comments')
                                                        @if($comment->dated && $comment->time)
                                                            <x-anchor-tag class="dropdown-item" href="#"
                                                                wire:click="edit({{ $comment?->id }})">
                                                                <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                                <span>Edit</span>
                                                            </x-anchor-tag>
                                                            @endif
                                                        @endcan
                                                        @can('delete_comments')
                                                            <x-anchor-tag class="dropdown-item" href="#"
                                                                wire:click="deleteConfirmation({{ $comment?->id }})">
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
                        {{ $comments->links('components.pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @can('add_tasks')
        <x-main-modal wireIgnoreSelf="wire:ignore.self">
            @include('livewire.backend.comment.form')
        </x-main-modal>
    @endcan
</div>
