<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="d-flex justify-center align-center gap-2 mt-4">

            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="text-muted mr-3" aria-hidden="true">
                    &laquo; Previous
                </span>
                <span class="btn-pagination disabled" aria-disabled="true">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="text-muted mr-3 btn-link"
                    style="background:none; border:none; cursor:pointer;">
                    &laquo; Previous
                </button>
                <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="btn-pagination">
                    <i class="fas fa-chevron-left"></i>
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="btn-pagination disabled">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn-pagination active" aria-current="page">{{ $page }}</span>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="btn-pagination">{{ $page }}</button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="btn-pagination">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <span class="btn-pagination disabled" aria-disabled="true">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </nav>
    @endif
</div>
