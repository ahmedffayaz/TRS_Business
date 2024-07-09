    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h4 class="card-title">Leaves</h4>
                        @can('add_leaves')
                        <div>
                            <x-anchor-tag href="#" class="btn btn-primary float-end" tabindex="0"
                                aria-controls="table-hover" type="button" wire:click="openModal" value="Add Leave" />
                        </div>
                        @endcan
                    </div>
                    <x-table-search :dataCounter="[]" />
                    <div class="card-table table-responsive card-min-height">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>No of Days</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @isset($leaves)
                                @forelse ($leaves as $leave)
                                @php
                                $start = \Carbon\Carbon::parse($leave->start_date);
                                $end = \Carbon\Carbon::parse($leave->end_date);
                                $numberOfDays = $end->diffInDays($start);
                                @endphp
                                <tr>
                                    <td>{{ $leave->id }}</td>
                                    <td>
                                        <div class="design-group">
                                            <div data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                data-bs-placement="bottom" title="{{ $leave?->user?->fullName }}"
                                                class="avatar bg-light-{{ randomColors() }} pull-up">
                                                @if ($leave?->user?->avatar)
                                                <img src="{{ getUserAvatar($leave?->user) }}" alt="Avatar" width="33"
                                                    height="33" />
                                                @else
                                                <div class="avatar-content">{{ $leave?->user?->avatarName }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ formatDate($leave->start_date) }}</td>
                                    <td>{{ formatDate($leave->end_date) }}</td>
                                    <td>{{ $numberOfDays }}</td>
                                    <td>
                                        <span
                                            class="badge rounded-pill badge-light-{{ leaveStatus($leave?->status) }}">{{
                                            ucfirst(str_replace('-', ' ', $leave?->status?->value)) }}</span>
                                    </td>

                                    @if(auth()->user()->can('cancel_leaves') || auth()->user()->can('edit_leaves')
                                    ||
                                    auth()->user()->can('view_reason') || auth()->user()->can('approve_leaves') ||
                                    auth()->user()->can('reject_leaves'))
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                data-bs-toggle="dropdown" wire:ignore.>
                                                <span><i data-feather="more-vertical"></i></span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('edit_leaves')
                                                <x-anchor-tag class="dropdown-item" href="#"
                                                    wire:click="edit({{ $leave?->id }})">
                                                    <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                                                    <span>Edit</span>
                                                </x-anchor-tag>
                                                @endcan
                                                @can('view_reason')
                                                <x-anchor-tag class="dropdown-item" href="#"
                                                    wire:click="viewReason({{ $leave?->id }})">
                                                    <span wire:ignore><i data-feather="eye" class="me-50"></i></span>
                                                    <span>View Reason</span>
                                                </x-anchor-tag>
                                                @endcan
                                                @if ( !in_array($leave->status, [$canceledStatus, $approvedStatus,
                                                $rejectedStatus]) && $leave->status == $pendingStatus)
                                                @can('cancel_leaves')
                                                @php
                                                $user = auth()->user()->id;
                                                @endphp
                                                @if($user && $leave->status == $pendingStatus && $leave->user_id ==
                                                $user)
                                                <x-anchor-tag class="dropdown-item" href="#"
                                                    wire:click="CancelConfirmation({{ $leave?->id }})">
                                                    <span wire:ignore><i data-feather="x" class="me-50"></i></span>
                                                    <span>Cancel</span>
                                                    @endif
                                                </x-anchor-tag>
                                                @endcan
                                                @can('approve_leaves')
                                                <x-anchor-tag class="dropdown-item" href="#"
                                                    wire:click="ApproveConfirmation({{ $leave?->id }})">
                                                    <span wire:ignore><i data-feather="check" class="me-50"></i></span>
                                                    <span>Approve</span>
                                                </x-anchor-tag>
                                                @endcan
                                                @can('reject_leaves')
                                                <x-anchor-tag class="dropdown-item" href="#"
                                                    wire:click="RejectConfiramtion({{ $leave?->id }})">
                                                    <span wire:ignore><i data-feather="x-circle"
                                                            class="me-50"></i></span>
                                                    <span>Reject</span>
                                                </x-anchor-tag>
                                                @endcan
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @else
                                    <td>
                                        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                            data-bs-toggle="dropdown">
                                            <span wire:ignore.>
                                                <i data-feather='lock'></i>
                                            </span>
                                        </button>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr class="no-hover">
                                    <td colspan="8" class="text-center py-1 fw-bold">
                                        <p>No Leave Found</p>
                                    </td>
                                </tr>
                                @endforelse
                                @endisset
                            </tbody>
                        </table>
                        {{ $leaves->links('components.pagination') }}
                    </div>
                </div>
            </div>
        </div>
        @if ($isShowReason)
        @can('view_reason')
        <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="model-sm" modalTitle="Reason">
            @include('livewire.backend.leaves.view-reason' ,compact('reason'))
        </x-main-modal>
        @endcan
        @elseif($cancelReason || $rejectReason)
        <x-main-modal wireIgnoreSelf="wire:ignore.self" modalSize="model-sm"
            modalTitle="Write Reason for Leave {{ $cancelReason ? 'Cancelation' : 'Rejection' }}">
            @include('livewire.backend.leaves.reason', compact('cancelReason'))
        </x-main-modal>
        @else
        @can('view_leaves')
        <x-main-modal wireIgnoreSelf="wire:ignore.self">
            @include('livewire.backend.leaves.form')
        </x-main-modal>
        @endcan
        @endif
    </div>
    @script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.dispatch('feather-icons');
            Livewire.on('reinitialize-icons', () => {
                Livewire.dispatch('feather-icons');
            });


            // Reinitialize flatpickr
            Livewire.dispatch('flatpickr');
            Livewire.on('reinitialize-dispatcher', () => {
                $(document).ready(function () {
                    Livewire.dispatch('flatpickr');
                    Livewire.dispatch('feather-icons');
                })
            })
        });
    </script>
    @endscript
