@assets
    <style>
        html .content {
            margin-left: 0px;
        }

        .header-navbar.fixed-top,
        .header-navbar.floating-nav {
            left: 0px;
        }

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
    </style>
@endassets
<div>
    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="list-group card-width">
                @foreach ($businesses as $business)
                    <a wire:click="selectBusiness('{{ $business->name }}')"
                        class="list-group-item list-group-item-action d-flex">
                        <div class="avatar me-75">
                            <img src="{{ asset('storage/' . $business->logo) }}" class="rounded" width="60"
                                height="60" alt="Avatar" onerror="_business_logo(this)" />
                        </div>
                        <div class="my-auto">
                            <h4 class="mb-0">{{ $business->name }}</h4>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        {{ $businesses->links('components.pagination', ['align' => 'justify-content-center']) }}
    </div>
</div>
