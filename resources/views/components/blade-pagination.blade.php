@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="justify-content-end pagination mt-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item prev-item disabled">
                    <a class="page-link" href="#"></a>
                </li>
            @else
                <li>
                    <a class="page-item prev-item" href="{{ $paginator->previousPageUrl() }}">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page">
                                <a class="page-link" href="#">{{ $page }}</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item next-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}"></a>
                </li>
            @else
                <li class="dpage-item next-item disabled">
                    <a class="page-link" href="#"></a>
                </li>
            @endif
        </ul>
    </nav>
@endif
