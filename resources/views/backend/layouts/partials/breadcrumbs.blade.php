@php
    $breadcrumbs = isset($uniqueBreadcrumbs) ? $uniqueBreadcrumbs : $pageBreadcrumbs;
    $currentUrl = request()->url();
    foreach ($breadcrumbs as $breadcrumb) {
        $pageTitle = ucwords(str_replace('-', ' ', $breadcrumb['title']));
    }
@endphp
<div class="content-header-left col-md-9 col-12 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">{{ $pageTitle }}</h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        <li class="breadcrumb-item {{ ($breadcrumb['url'] == null || $breadcrumb['url'] == $currentUrl) ? 'active' : '' }}"
                            aria-current="page">
                            @if($breadcrumb['url'] != null)
                                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                            @else
                                {{ $breadcrumb['title'] }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
