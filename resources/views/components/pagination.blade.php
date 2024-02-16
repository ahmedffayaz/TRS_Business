<div>
    @if ($paginator?->hasPages())
        <nav aria-label="Page navigation">
            <ul class="justify-content-end pagination mt-2">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item prev-item disabled"><a class="page-link" href="#"></a></li>
                @else
                    <li class="page-item prev-item"><a class="page-link" href="{{ $paginator->previousPageUrl() }}"
                            wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                            wire:navigate></a>
                    </li>
                @endif

                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="page-item disabled" aria-disabled="true"><span
                                class="page-link">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="page-item active"
                                    wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"
                                    aria-current="page">
                                    <a class="page-link" href="#">{{ $page }}</a>
                                </li>
                            @else
                                <li class="page-item"
                                    wire:key="paginator-{{ $paginator->getPageName() }}-page-{{ $page }}"><a
                                        class="page-link" href="?page={{ $page }}"
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                        wire:navigate>{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item next-item"><a class="page-link" href="{{ $paginator->nextPageUrl() }}"
                            wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                            wire:navigate></a>
                    </li>
                @else
                    <li class="page-item next-item disabled"><a class="page-link" href="#"></a></li>
                @endif
            </ul>
        </nav>
    @endif
</div>
