<div class="content-header-left col-md-8 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $page_title }}</h2>
            @if(count($breadcrumbs) > 1)
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </div>
            @endif
        </div>
    </div>
</div>
