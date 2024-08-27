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
                @role('client')
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="card card-developer-meetup">
                            @php
                                $business_details = session('business_details');
                                $src =
                                    $business_details['logo'] != null &&
                                    Storage::disk('public')->exists($business_details['logo'])
                                        ? 'storage/' . $business_details['logo']
                                        : cmsLogo();
                            @endphp
                            <div class="meetup-img-wrapper rounded-top text-center d-flex justify-content-center align-items-center position-relative"
                                style="width: 100%; height: 170px;">
                                @if ($src)
                                    <img src="{{ $src }}" alt="logo"
                                        style="max-height: 100%; max-width: 100%; object-fit: contain;" wire:ignore />
                                    <!-- Edit Icon in a Circle -->
                                    <div class="edit-icon position-absolute bg-primary d-flex justify-content-center align-items-center rounded-circle cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit company details"
                                        style="width: 40px; height: 40px; bottom: 10px; right: 10px;" wire:click="editClient({{ $clientDetail?->id }})" wire:ignore.self>
                                        <i data-feather="edit-2" style="font-size: 1.5rem; color: #ffffff;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="meetup-header d-flex align-items-center">
                                    <div class="meetup-day" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        title="Registration day" wire:ignore.self>
                                        <h6 class="mb-0">{{ $clientDetail?->created_at->format('D') }}</h6>
                                        <h3 class="mb-0">{{ $clientDetail?->created_at->format('d') }}</h3>
                                    </div>
                                    <div class="my-auto">
                                        <h4 class="card-title mb-25">{{ $clientDetail?->name }}</h4>
                                        <p class="card-text mb-0"> <strong>Business:</strong>
                                            &nbsp;{{ $clientDetail?->business?->name }}</p>
                                    </div>
                                </div>
                                <div class="mt-0">
                                    <div class="avatar float-start bg-light-primary rounded me-1">
                                        <div class="avatar-content">
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
                @endrole
                <!-- Statistics Card -->
                <div
                    class="col-lg-8 col-md-6 col-12 {{ auth()->user()->hasRole('client') ? 'col-lg-8' : 'col-lg-12' }}">
                    <div class="row">
                        @role('admin|client')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <a href="{{ route('dashboard.users.index') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Click to view the list of all users" wire:ignore.self>
                                            <div class="avatar bg-light-primary p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="users" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h2 class="fw-bolder">{{ $userCount }}</h2>
                                            <p class="card-text">Users</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endrole
                        @role('admin')
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <div class="card text-center">
                                    <a href="{{ route('dashboard.clients.index') }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Click to view the list of all users" wire:ignore.self>
                                        <div class="card-body">
                                            <div class="avatar bg-light-info p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="user-check" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h2 class="fw-bolder">{{ $clientCount }}</h2>
                                            <p class="card-text">Clients</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endrole
                        @role('admin|client')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <a href="{{ route('dashboard.projects.index') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="left" title="Click to view the list of all projects" wire:ignore.self>
                                            <div class="avatar bg-light-info p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="file-text" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h2 class="fw-bolder">{{ $projectCount }}</h2>
                                            <p class="card-text">Projects</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endrole
                        @role('admin|client')
                            <div
                                class="col-md-4 col-sm-6 {{ auth()->user()->hasRole('client') ? 'col-xl-4' : 'col-xl-3' }}">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <a href="{{ route('dashboard.tasks.index') }}" data-bs-toggle="tooltip"
                                            data-bs-placement="right" title="Click to view the list of all tasks" wire:ignore.self>
                                            <div class="avatar bg-light-info p-50 mb-1">
                                                <div class="avatar-content">
                                                    <i data-feather="check-circle" class="font-medium-5"></i>
                                                </div>
                                            </div>
                                            <h2 class="fw-bolder">{{ $taskCount }}</h2>
                                            <p class="card-text">Tasks</p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endrole
                    </div>
                </div>
            </div>

        </section>
    </div>

    {{-- client eidt modal  --}}
    @role('client')
    <x-main-modal wireIgnoreSelf="wire:ignore.self" closeModal="closeMainModal" modalTitle="Edit company details">
        <form wire:submit.prevent="updateClientDetail">
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
                    <x-input-label for="address" class="required" value="Company name" />
                    <x-textarea type="text" id="address" :class="$errors->has('form.clientAddress') ? 'error' : ''" placeholder="Enter company name" wire:model="form.clientAddress"
                        value="{{ $clientDetail?->address }}" />
                    @error('form.clientAddress')
                        <x-input-error :message="$message" />
                    @enderror
                </div>
                <div class="col-md-12 text-center">
                    <x-button class="btn btn-primary me-1 waves-effect waves-float waves-light" type="submit" tabindex="4"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>Update</span>
                        <span wire:loading>
                            <i class="fa fa-spinner fa-spin"></i> {{ __('Loading...') }}
                        </span>
                    </x-button>
                </div>
            </div>
        </form>
    </x-main-modal>
@endcan
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
@script
    <script type="module">
        $(document).ready(function () {
            // Reinitialize icons
            Livewire.on('reinitialize-icons', () => {
                $(document).ready(function () {
                    Livewire.dispatch('feather-icons');
                    Livewire.dispatch('select-container');
                    $('[data-bs-toggle="tooltip"]').tooltip({
                        container: 'body'
                    })
                });
            });

            $(document).on('change','#clientContryId', function(e) {
                @this.set('form.clientContryId', e.target.value);
            });
        })
    </script>
@endscript
