@php
    if (!isset($scrollTo)) {
        $scrollTo = 'body';
    }

    $scrollIntoViewJsSnippet = ($scrollTo !== false)
        ? <<<JS
               (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
            JS
        : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200/80 cursor-default rounded-lg opacity-50">
                    <i class="fas fa-chevron-left text-[10px] mr-1"></i> Prev
                </span>
            @else
                @if(method_exists($paginator, 'getCursorName'))
                    <button type="button" dusk="previousPage"
                        wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->previousCursor()->encode() }}"
                        wire:click="setPage('{{$paginator->previousCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200/80 rounded-lg hover:bg-violet-500 hover:border-violet-500 hover:text-white transition-all duration-150">
                        <i class="fas fa-chevron-left text-[10px] mr-1"></i> Prev
                    </button>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200/80 rounded-lg hover:bg-violet-500 hover:border-violet-500 hover:text-white transition-all duration-150">
                        <i class="fas fa-chevron-left text-[10px] mr-1"></i> Prev
                    </button>
                @endif
            @endif

            {{-- Page Info --}}
            <span class="text-xs text-slate-500 font-medium px-2">
                Halaman {{ $paginator->currentPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                @if(method_exists($paginator, 'getCursorName'))
                    <button type="button" dusk="nextPage"
                        wire:key="cursor-{{ $paginator->getCursorName() }}-{{ $paginator->nextCursor()->encode() }}"
                        wire:click="setPage('{{$paginator->nextCursor()->encode()}}','{{ $paginator->getCursorName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200/80 rounded-lg hover:bg-violet-500 hover:border-violet-500 hover:text-white transition-all duration-150">
                        Next <i class="fas fa-chevron-right text-[10px] ml-1"></i>
                    </button>
                @else
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')"
                        x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled"
                        dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200/80 rounded-lg hover:bg-violet-500 hover:border-violet-500 hover:text-white transition-all duration-150">
                        Next <i class="fas fa-chevron-right text-[10px] ml-1"></i>
                    </button>
                @endif
            @else
                <span
                    class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-100 border border-slate-200/80 cursor-default rounded-lg opacity-50">
                    Next <i class="fas fa-chevron-right text-[10px] ml-1"></i>
                </span>
            @endif
        </nav>
    @endif
</div>
