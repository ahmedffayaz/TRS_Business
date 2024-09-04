@section('breadcrumbs', Breadcrumbs::render('dashboard'))
@push('styles')
    <style>
        .avatar-img {
            width: 33px;
            height: 33px;
            object-fit: cover;
            border-radius: 50%;
            overflow: hidden;
        }

        .meetup-img-wrapper .edit-icon {
            opacity: 0;
            transition: opacity 1s;
        }

        .meetup-img-wrapper:hover .edit-icon {
            opacity: 1;
        }
        .congratulation-medal-img {
            position: absolute;
            bottom: 0;
            right: 25px;
        }
        .custume-size {
            width: 35px !important;
            height: 40px !important;
        }
    </style>
@endpush
<div>
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section id="dashboard-ecommerce">
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
            <div class="row match-height">
                @can('view_client_dashboard')
                @if(!auth()->user()->hasRole('admin'))
                <div class="col-xl-6 col-md-6 col-12">
                    <div class="card card-congratulation-medal">
                        <div class="card-body">
                            <h3 class="fw-bolder">Welcome Back {{ auth()->user()->name  }}</h3>
                            <p class="card-text font-small-3">Your Subscription plan require updates</p>
                            <span>
                                <div class="progress-wrapper mb-2"  style="width: 65%;">
                                    <div id="example-caption-2" class="d-flex justify-content-between">
                                        <span>Days</span>
                                        <span>26 of 30 Days</span>
                                    </div>
                                    <div class="progress progress-bar-primary" style="height: 10px;">
                                        <div class="progress-bar" role="progressbar" aria-valuenow="25" aria-valuemin="25" aria-valuemax="30" style="width: 25%" aria-describedby="example-caption-2"></div>
                                    </div>
                                </div>
                            </span>
                            <button type="button" class="btn btn-primary waves-effect waves-float waves-light">Upgrade Plan</button>
                            <img src="{{ asset('assets/images/card-advance-sale.png') }}" class="congratulation-medal-img">
                        </div>
                    </div>
                </div>
                @endif
                @endcan
                <!-- Statistics Card -->
                <div
                    class="col-md-6 col-12 {{ auth()->user()->hasRole('admin') ? 'col-lg-12' :  'col-lg-6' }}">
                    <div class="row">
                        @can('view_client_dashboard')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body" style="padding:2.4rem;">
                                        <a href="{{ route('dashboard.users.index') }}" data-bs-toggle="tooltip" style="margin-top: 20px;margin-bottom: 20px;"
                                            data-bs-placement="top" title="Click to view the list of all users" wire:ignore.self>
                                            <div class="avatar bg-light-primary p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="users" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <p class="card-text text-black">Total Users</p>
                                            <h1 class="fw-bolder text-primary">{{ $userCount }}</h1>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @role('admin')
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="card text-center">
                                    <a href="{{ route('dashboard.clients.index') }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Click to view the list of all users" wire:ignore.self>
                                        <div class="card-body" style="padding:2.4rem;">
                                            <div class="avatar bg-light-info p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="user-check" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <p class="card-text text-black">Total Clients</p>
                                            <h1 class="fw-bolder text-info">{{ $clientCount }}</h1>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endrole
                        @can('view_client_dashboard')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body" style="padding:2.4rem;">
                                        <a href="{{ route('dashboard.projects.index') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="left" title="Click to view the list of all projects" wire:ignore.self>
                                            <div class="avatar bg-light-info p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="file-text" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <p class="card-text text-black" >Total Projects</p>
                                            <h1 class="fw-bolder text-info">{{ $projectCount }}</h1>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view_client_dashboard')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body" style="padding:2.4rem;">
                                        <a href="{{ route('dashboard.tasks.index') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="Click to view the list of all tasks" wire:ignore.self>
                                            <div class="avatar bg-light-success p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="check-circle" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <p class="card-text text-black">Total Tasks</p>
                                            <h1 class="fw-bolder text-success">{{ $taskCount }}</h1>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>


            @can('view_client_dashboard')
            @if(!auth()->user()->hasRole('admin'))
            <div class="row match-height">
                <!-- task Table Card -->
                <div class="col-lg-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Upcoming tasks</h4>
                            <div class="col-md-3 col-sm-12">
                              <input type="search" class="form-control" wire:model.live="taskSearch" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search2" id="search" />
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="card-table table-responsive card-min-height">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Deadline</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @isset($this->tasks)
                                        @forelse ($this->tasks as $task)
                                        <tr>
                                            <td class="text-nowrap">
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
                                                                <a @if(strlen($task?->name) > 40)data-bs-toggle="tooltip"data-bs-placement="bottom"title="{{ ucwords($task?->name) }}" @endif
                                                                    href="{{ route('dashboard.tasks.view', $task->id) }}"
                                                                    style="color: inherit;"
                                                                    class="user_name text-truncate text-body fw-bolder">
                                                                    <span class="fw-bolder">{{ ucwords(Str::limit($task?->name, 40)) }}</span>
                                                                </a>

                                                                <span class="ms-1">{{ $task->comments->count() }}</span>
                                                                <img class="comment-icon"
                                                                    src="{{ asset('assets/images/Comment icon.png') }}"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="This task have {{ $task->comments->count() }} comments" />

                                                                <span wire:ignore data-bs-toggle="tooltip" data-bs-placement="bottom"
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

                                        </tr>
                                    @empty
                                        <tr class="no-hover">
                                            <td colspan="8" class="text-center py-1 fw-bold text-nowrap">
                                                <p>No Task Found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                    @endisset
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ task Table Card -->

                <!-- Client detail  Card -->
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-developer-meetup">
                        <div class="meetup-img-wrapper rounded-top text-center d-flex justify-content-center align-items-center position-relative"
                            style="width: 100%; height: 170px;">
                                <img src="{{ asset('assets/images/business_discution.png') }}" alt="logo"
                                    style="max-height: 100%; max-width: 100%; object-fit: contain;" wire:ignore />
                                <!-- Edit Icon in a Circle -->
                                <div class="edit-icon position-absolute bg-primary d-flex justify-content-center align-items-center rounded-circle cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit company details"
                                    style="width: 40px; height: 40px; bottom: 10px; right: 10px;" wire:click="editClient({{ $clientDetail?->id }})" wire:ignore.self>
                                    <i data-feather="edit-2" style="font-size: 1.5rem; color: #ffffff;"></i>
                                </div>

                        </div>
                        <div class="card-body">

                            <div class="d-flex align-items-center mt-0">
                                <div class="avatar float-start bg-light-primary rounded me-1">
                                    <div class="avatar-content custume-size">
                                        <i data-feather="file-text" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="more-info" style="margin-top: 10px;">
                                    <h4 style="font-weight: 500;font-size: 1.285rem;">{{ $clientDetail?->business?->name }}</h4>
                                </div>
                            </div>

                            <div class="mt-2 d-flex align-items-center">
                                <div class="avatar float-start bg-light-primary rounded me-1">
                                    <div class="avatar-content custume-size">
                                        <i data-feather="user" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="more-info" style="margin-top: 6px;">
                                    <h4 class="mb-0">{{ $clientDetail?->name }}</h4>
                                    <small class="">user@gmail.com</small>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="avatar float-start bg-light-primary rounded me-1">
                                    <div class="avatar-content custume-size">
                                        <i data-feather="calendar" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="more-info">
                                    <h6 class="mb-0">{{ $clientDetail?->created_at->format('D, M d, Y') }}</h6>
                                    <small>{{ $clientDetail?->created_at->format('h:A') }}</small>
                                </div>
                            </div>

                            <div class="mt-2">
                                <div class="avatar float-start bg-light-primary rounded me-1">
                                    <div class="avatar-content">
                                        <i data-feather="map-pin" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="more-info">
                                    <h6 class="mb-0">{{ $clientDetail?->city }} {{ $clientDetail?->country?->name }}
                                    </h6>
                                    <small>{{ $clientDetail?->address }}</small>
                                </div>
                            </div>

                            <div class="avatar-group">
                                @if ($clientDetail?->employees->isNotEmpty())
                                    @foreach ($clientDetail->employees->take(5) as $employee)
                                        <div data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                            data-bs-placement="bottom" title="{{ $employee?->fullName }}"
                                            class="avatar pull-up" wire:ignore.self>
                                            <img src="{{ getUserAvatar($employee) }}" alt="Avatar" class="avatar-img"
                                                width="33" height="33" />
                                        </div>
                                    @endforeach

                                    {{-- Calculate and show the total remaining count --}}
                                    @if ($clientDetail->employees->count() > 5)
                                        <h6 class="align-self-center cursor-pointer ms-50 mb-0">
                                            +{{ $clientDetail->employees->count() - 5 }}
                                        </h6>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!--/ end client detail Card -->
            </div>


            {{-- project table  --}}
            <div class="row match-height">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Upcoming projects</h4>
                            <div class="col-md-2 col-sm-12">
                              <input type="search" class="form-control" wire:model.live="search" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search2" id="search" />
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Deadline</th>
                                        <th>Status</th>
                                        <th>Members</th>
                                        <th>Tasks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @isset($this->projects)
                                    @forelse ($this->projects as $project)
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
                                    </tr>
                                    @empty
                                    <tr class="no-hover">
                                        <td colspan="7" class="text-center py-1 fw-bold">
                                            <p>No Project Found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                    @endisset
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>


            {{-- eide modal --}}
            <x-main-modal wireIgnoreSelf="wire:ignore.self"  modalTitle="Edit company details" formSubmit="updateClientDetail">
                {{-- <form wire:submit="updateClientDetail"> --}}
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <x-input-label for="clientName" class="required" value="Company name" />
                            <x-input type="text" id="clientName" :class="$errors->has('form.clientName') ? 'error' : ''" placeholder="Enter company name" wire:model="form.clientName"
                                value="{{ $clientDetail?->name }}" />
                            @error('form.clientName')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <span>
                                <x-input-label for="clientContryId" class="required" value="Country" />
                                <x-select-input id="clientContryId"
                                    :class="$errors->has('form.clientContryId') ? 'error select-two select2' : 'select-two select2'"
                                    wire:model="form.clientContryId">
                                    <option>Select country</option>
                                    @isset($this->countries)
                                        @foreach ($this->countries as $country)
                                            <option value="{{ $country->id }}" @selected($country->id == $form['clientContryId'])>{{ $country->name }}</option>
                                        @endforeach
                                    @endisset
                                </x-select-input>
                            </span>
                            @error('form.clientContryId')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <x-input-label for="clientCity" class="required" value="City" />
                            <x-input type="text"  id="clientCity" :class="$errors->has('form.clientCity') ? 'error' : ''" placeholder="Enter city" wire:model="form.clientCity"
                                value="{{ $clientDetail?->city }}" />
                            @error('form.clientCity')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <x-input-label for="clientPostalCode" value="Postal Code" />
                            <x-input type="number" id="clientPostalCode"
                                :class="$errors->has('form.clientPostalCode') ? 'error' : ''"
                                placeholder="Enter postal code" wire:model="form.clientPostalCode"  value="{{ $clientDetail?->postal_code }}"/>
                            @error('form.clientPostalCode')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-12 mb-1">
                            <x-input-label for="address" class="required" value="Address" />
                            <x-textarea type="text" id="address" :class="$errors->has('form.clientAddress') ? 'error' : ''" placeholder="Enter address" wire:model="form.clientAddress"
                                value="{{ $clientDetail?->address }}" />
                            @error('form.clientAddress')
                                <x-input-error :message="$message" />
                            @enderror
                        </div>
                        <div class="col-md-12 text-center d-flex justify-content-end">
                            <x-button class="btn btn-primary waves-effect waves-float waves-light" type="submit" tabindex="4"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Update</span>
                                <span wire:loading>
                                    <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                                </span>
                            </x-button>
                        </div>
                    </div>
                {{-- </form> --}}
            </x-main-modal>
            @endif
            @endcan
        </section>
    </div>



@if ($show_swl)
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
                Livewire.dispatch('accept_invitation_dashboard')
            } else {
                Livewire.dispatch('reject_invitation_dashboard')
            }
        });
    </script>
@endif

</div>

@push('scripts')
<script type="module">
    $(document).ready(function () {
        // Reinitialize icons
        Livewire.on('reinitialize-icons', () => {
            $(document).ready(function () {
                Livewire.dispatch('feather-icons');
                Livewire.dispatch('select-container');
                // $('[data-bs-toggle="tooltip"]').tooltip({
                //     container: 'body'
                // });
            });
        });

        $(document).on('change','#clientContryId', function(e) {
            @this.set('form.clientContryId', e.target.value);
        });
    });
</script>
@endpush

