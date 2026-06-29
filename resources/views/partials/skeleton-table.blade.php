@php
    $filasSkeleton = max((int) ($perPage ?? 15), 1);
    $anchosCelda = ['72%', '48%', '88%', '56%', '64%', '40%'];
    $anchosCabecera = ['5rem', '6.5rem', '4.5rem', '5.5rem', '4rem', '7rem'];
    $mostrarPie = ($tienePerPage ?? true) || ($tienePaginacion ?? true);
@endphp

<div class="panel-table-wrap panel-skeleton-table {{ $tableClasses ?? '' }}" aria-hidden="true" aria-busy="true">
    <div class="panel-table-scroll overflow-x-auto">
        <table class="panel-table {{ $tableClasses ?? '' }}">
            <thead>
                <tr>
                    @if ($hasBulkActions ?? false)
                        <th class="w-10">
                            <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--check"></span>
                        </th>
                    @endif
                    @foreach ($columns as $indice => $column)
                        <th>
                            <span
                                class="panel-skeleton panel-skeleton-line panel-skeleton-line--label"
                                style="width: {{ $anchosCabecera[$indice % count($anchosCabecera)] }}"
                            ></span>
                        </th>
                    @endforeach
                    <th class="panel-table-actions-col">
                        <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--icon"></span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($fila = 0; $fila < $filasSkeleton; $fila++)
                    <tr>
                        @if ($hasBulkActions ?? false)
                            <td>
                                <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--check"></span>
                            </td>
                        @endif
                        @foreach ($columns as $indice => $column)
                            <td>
                                @if (in_array($column->getType(), ['image', 'avatar'], true))
                                    <span class="panel-skeleton panel-skeleton-thumb"></span>
                                @elseif (in_array($column->getType(), ['boolean', 'badge'], true))
                                    <span class="panel-skeleton panel-skeleton-pill"></span>
                                @else
                                    <span
                                        class="panel-skeleton panel-skeleton-line"
                                        style="width: {{ $anchosCelda[($indice + $fila) % count($anchosCelda)] }}"
                                    ></span>
                                @endif
                            </td>
                        @endforeach
                        <td class="panel-table-actions-col">
                            <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--icon"></span>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    @if ($mostrarPie)
        <div class="panel-table-footer panel-skeleton-table__footer">
            @if ($tienePerPage ?? true)
                <div class="panel-skeleton-table__footer-left">
                    <span class="panel-skeleton panel-skeleton-line panel-skeleton-line--xs"></span>
                    <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--select"></span>
                </div>
            @endif
            @if ($tienePaginacion ?? true)
                <div class="panel-skeleton-table__footer-right">
                    <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--page"></span>
                    <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--page"></span>
                    <span class="panel-skeleton panel-skeleton-box panel-skeleton-box--page"></span>
                </div>
            @endif
        </div>
    @endif
</div>
