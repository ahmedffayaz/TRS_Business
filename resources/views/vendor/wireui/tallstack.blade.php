<div
    class="content-header-left col-md-8 mb-2"
    x-data="{
        breadcrumbs: @js($breadcrumbs),

        init() {
            document.addEventListener('wireui::breadcrumbs', ({ detail }) => {
                this.breadcrumbs = detail
            })
        }
}"
>
    <div class="row breadcrumbs-top">
        <div class="col-12">
            <h2 class="content-header-title float-start mb-0">
                @foreach ($breadcrumbs as $index => $breadcrumb)
                    @if ($index === count($breadcrumbs) - 1)
                        {{ $breadcrumb['label'] }}
                    @endif
                @endforeach
            </h2>
            <div class="breadcrumb-wrapper">
                <ol class="breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if (!$loop->last)
                            @if ($loop->index === count($breadcrumbs) - 2)
                                <x-nav class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['label'] }}</x-nav>
                            @else
                                <x-nav class="breadcrumb-item">
                                    <x-anchor-tag href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</x-anchor-tag>
                                </x-nav>
                            @endif
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
