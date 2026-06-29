@php
    $filas = min((int) ($perPage ?? 5), 5);
@endphp

<div class="panel-record-cards panel-skeleton-cards panel-only-mobile" aria-hidden="true" aria-busy="true">
    @for ($i = 0; $i < $filas; $i++)
        <div class="panel-record-card panel-skeleton-card">
            <div class="panel-skeleton-card__line panel-skeleton-card__line--title"></div>
            <div class="panel-skeleton-card__line"></div>
            <div class="panel-skeleton-card__line panel-skeleton-card__line--short"></div>
        </div>
    @endfor
</div>
