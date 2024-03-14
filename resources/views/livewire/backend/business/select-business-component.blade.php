@assets
    <style>
        html .content {
            margin-left: 0px;
        }

        .header-navbar.fixed-top, .header-navbar.floating-nav {
            left: 0px;
        }

        @media screen and (max-width: 280px) {
            .card-width {
                width: 230px;
            }
        }

        @media screen and (min-width: 281px) and (max-width: 539px) {
            .card-width {
                width: 300px;
            }
        }

        @media screen and (min-width: 540px) {
            .card-width {
                width: 400px;
            }
        }
    </style>
@endassets
<div>
    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="card card-employee-task card-width mb-1">
                <div class="card-body">
                    @foreach($businesses as $business)
                        <a wire:click="selectBusiness('{{ $business->name }}')" class="brand-logo">
                            <div class="employee-task d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex flex-row">
                                    <div class="avatar me-75">
                                        <img src="{{ asset('storage/' . $business->logo) }}" class="rounded" width="42" height="42" alt="Avatar" onerror="_business_logo(this)" />
                                    </div>
                                    <div class="my-auto">
                                        <h6 class="mb-0">{{ $business->name }}</h6>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @if (!$loop->last)
                            <hr>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        {{ $businesses->links('components.pagination', ['align' => 'justify-content-center']) }}
    </div>
</div>
