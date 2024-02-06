@php
    $url = url('/');
        $currentUrlWithoutBase = str_replace($url, '', url()->current());
        $segments = explode('/', trim($currentUrlWithoutBase, '/'));
        $breadcrumbs = [];
        if(isset($segment[0]) && $segment[0] == 'dashboard'){
            $breadcrumbs = [
                ['url' => url('/'), 'label' => 'Dashboard'],
            ];
        }
        foreach($segments as $segment){
           $page = ['url' => asset($segment), 'label' => ucwords(str_replace('-', ' ', $segment))];
           array_push($breadcrumbs, $page);
           $page_title = ucwords(str_replace('-', ' ', $segment));
        }
        $breadcrumbs = $breadcrumbs;
@endphp
<div class="content-header-left col-md-9 col-12 mb-2">
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
