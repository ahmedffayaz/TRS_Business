@section('breadcrumbs', Breadcrumbs::render('Businesses'))
@assets
<style>
    @media screen and (max-width: 539px) {
        .card-width {
            width: 100%;
        }
    }

    @media screen and (min-width: 540px) {
        .card-width {
            width: 600px;
        }
    }

    .list-group .list-group-item-action.hover h1,
    .list-group .list-group-item-action.hover h2,
    .list-group .list-group-item-action.hover h3,
    .list-group .list-group-item-action.hover h4,
    .list-group .list-group-item-action.hover h5,
    .list-group .list-group-item-action.hover h6,
    .list-group .list-group-item-action:hover h1,
    .list-group .list-group-item-action:hover h2,
    .list-group .list-group-item-action:hover h3,
    .list-group .list-group-item-action:hover h4,
    .list-group .list-group-item-action:hover h5,
    .list-group .list-group-item-action:hover h6 {
        color: #FFF;
    }

    .list-group-item:hover {
        background: linear-gradient(118deg, #7367F0, rgba(115, 103, 240, .7));
        color: #fff;
    }

    .btn.login {
        background-color: #7367F0;
        color: white;
    }

    .btn.login:hover {
        background-color: white;
        color: #7367F0;
    }

    .avatar img {
        object-fit: cover;
    }

    .rounded-circle {
        border-radius: 50%;
    }
</style>
@endassets
<div class="container">
    @section('breadcrumbs-button')
    <x-anchor-tag href="#" class="btn btn-primary float-end" tabindex="0"
    aria-controls="table-hover" type="button" wire:click="openCreateBusinessModal" :value="__('Add Business')" wire.ignore />
    @endsection
    <div class="row">
        @foreach ($businesses as $business)
        <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
            <div class="card h-100 position-relative">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar me-3">
                        <img src="{{ asset('storage/' . $business->logo) }}" class="rounded-circle" width="60"
                            height="60" alt="Avatar" onerror="_business_logo(this)" />
                    </div>
                    <div class="">
                        <h5 class="card-title mb-2">{{ $business->name }}</h5>
                        <h6 class="">{{ $business->address }}</h6>

                        <button class="btn btn-sm btn-primary mt-auto">Login</button>
                    </div>
                </div>
                <div class="dropdown position-absolute top-0 end-0 my-1 ">
                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span><i data-feather="more-vertical"></i></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        @can('edit_leaves')
                        <x-anchor-tag class="dropdown-item" href="#" wire:click="edit({{ $business?->id }})">
                            <span wire:ignore><i data-feather="edit-2" class="me-50"></i></span>
                            <span>Edit</span>
                        </x-anchor-tag>
                        @endcan
                        @can('view_reason')
                        <x-anchor-tag class="dropdown-item" href="#" wire:click="viewReason({{ $business?->id }})">
                            <span wire:ignore><i data-feather="trash" class="me-50"></i></span>
                            <span>Delete</span>
                        </x-anchor-tag>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col text-center">
            {{ $businesses->links('components.blade-pagination') }}
        </div>
    </div>
    <x-main-modal wireIgnoreSelf="wire:ignore.self">
        @include('livewire.backend.business.form' ,compact('countries'))
    </x-main-modal>
</div>
