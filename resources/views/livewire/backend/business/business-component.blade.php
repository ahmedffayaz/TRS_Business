@assets
    <style>
        html .content {
            margin-left: 0px;
        }

        .header-navbar.floating-nav {
            right: auto;
            margin-left: -27px;
        }
    </style>
@endassets
<div>
    <div class="row">
        @foreach($businesses as $business)
            <div class="col-md-4 mb-2">
                <a wire:click="selectBusiness('{{ $business->name }}')" class="brand-logo">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/images/business/' . $business->logo) }}" class="mb-2"
                                alt="" height="60" onerror="_business_logo(this)">
                            <h4 class="card-title mb-1 text-center">{{ $business->name }}</h4>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach

        {{ $businesses->links('components.pagination') }}
    </div>
</div>
