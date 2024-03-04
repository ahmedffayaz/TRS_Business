<div class="content-header-left col-md-8 mb-2">
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">
                @foreach ($breadcrumbs as $index => $breadcrumb)
                    @if ($index === count($breadcrumbs) - 1)
                        {{ $breadcrumb->title }}
                    @endif
                @endforeach
            </h2>
            <div class="breadcrumb-wrapper">
                @if (count($breadcrumbs))
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($breadcrumb->url && !$loop->last)
                            <x-nav class="breadcrumb-item">
                                <x-anchor-tag href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</x-anchor-tag>
                            </x-nav>
                        @else
                            <x-nav class="breadcrumb-item active" aria-current="page">{{ $breadcrumb->title }}</x-nav>
                        @endif
                    @endforeach
                </ol>
                @endif
            </div>
        </div>
    </div>
</div>
