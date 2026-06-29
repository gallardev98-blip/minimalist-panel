@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('panel::panel.pagination') }}" class="panel-pagination">
        <p class="panel-pagination__range panel-muted text-xs">
            {{ __('panel::panel.showing') }}
            <span class="panel-heading font-medium">{{ $paginator->firstItem() }}</span>
            –
            <span class="panel-heading font-medium">{{ $paginator->lastItem() }}</span>
            {{ __('panel::panel.of') }}
            <span class="panel-heading font-medium">{{ $paginator->total() }}</span>
        </p>

        <div class="panel-pagination__controls flex items-center gap-0.5">
            @if ($paginator->onFirstPage())
                <span class="panel-pagination-btn" aria-disabled="true">{{ __('panel::panel.previous') }}</span>
            @else
                <button
                    type="button"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="panel-pagination-btn"
                >
                    {{ __('panel::panel.previous') }}
                </button>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="panel-pagination-btn panel-pagination-btn-active">{{ $page }}</span>
                @else
                    <button
                        type="button"
                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                        class="panel-pagination-btn"
                    >
                        {{ $page }}
                    </button>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="panel-pagination-btn"
                >
                    {{ __('panel::panel.next') }}
                </button>
            @else
                <span class="panel-pagination-btn" aria-disabled="true">{{ __('panel::panel.next') }}</span>
            @endif
        </div>
    </nav>
@endif
