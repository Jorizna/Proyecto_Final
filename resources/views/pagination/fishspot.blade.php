@if ($paginator->hasPages())
<nav class="pagination-nav" aria-label="Paginación">

    @if ($paginator->onFirstPage())
        <span class="pagination-nav__btn pagination-nav__btn--disabled">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Anterior
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="pagination-nav__btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Anterior
        </a>
    @endif

    <span class="pagination-nav__info">
        Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
    </span>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="pagination-nav__btn">
            Siguiente
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </a>
    @else
        <span class="pagination-nav__btn pagination-nav__btn--disabled">
            Siguiente
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="9 18 15 12 9 6"/>
            </svg>
        </span>
    @endif

</nav>
@endif
