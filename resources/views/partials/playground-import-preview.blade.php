@php
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $importActivo = (bool) config('panel.import.enabled', true);
    $vistaPrevia = (bool) config('panel.import.preview', true);
    $upsert = (bool) config('panel.import.upsert', false);
    $filas = PlaygroundDemo::filasImportPreview();
    $validas = count(array_filter($filas, fn (array $fila): bool => $fila['valid']));
@endphp

<x-panel::playground-zona zona="import" :$zonasModificadas :$zonaResaltada>
    <div class="panel-card overflow-hidden">
        <div class="panel-border flex flex-wrap items-center justify-between gap-2 border-b px-4 py-3">
            <div>
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.import_preview_title') }}</h2>
                <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.import_preview_desc') }}</p>
            </div>
            <div class="flex flex-wrap gap-1.5">
                <span @class(['panel-badge text-xs', $importActivo ? 'panel-badge-success' : 'panel-badge-muted'])>
                    {{ $importActivo ? __('panel::panel.documentation.import_preview_on') : __('panel::panel.documentation.import_preview_off') }}
                </span>
                @if ($upsert)
                    <span class="panel-badge panel-badge-primary text-xs">Upsert</span>
                @endif
            </div>
        </div>

        <div class="p-4">
            @if (! $importActivo)
                <p class="panel-muted text-sm">{{ __('panel::panel.documentation.import_preview_disabled') }}</p>
            @elseif ($vistaPrevia)
                <p class="panel-muted mb-3 text-xs">
                    {{ __('panel::panel.import.preview_summary', ['valid' => $validas, 'invalid' => count($filas) - $validas, 'total' => count($filas)]) }}
                </p>
                <div class="panel-import-preview-table-wrap">
                    <table class="panel-table panel-import-preview-table w-full text-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('panel::panel.documentation.col_name') }}</th>
                                <th>{{ __('panel::panel.documentation.import_preview_price') }}</th>
                                <th>{{ __('panel::panel.documentation.col_status') }}</th>
                                <th>{{ __('panel::panel.import.preview_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filas as $indice => $fila)
                                <tr @class(['panel-import-preview-row--invalid' => ! $fila['valid']])>
                                    <td>{{ $indice + 1 }}</td>
                                    @foreach ($fila['cells'] as $celda)
                                        <td>{{ $celda !== '' ? $celda : '—' }}</td>
                                    @endforeach
                                    <td>
                                        @if ($fila['valid'])
                                            <span class="panel-badge panel-badge-success text-xs">{{ __('panel::panel.import.preview_valid') }}</span>
                                        @else
                                            <span class="panel-badge panel-badge-danger text-xs">{{ __('panel::panel.import.preview_invalid') }}</span>
                                            @if (! empty($fila['error']))
                                                <p class="panel-field-error mt-1 text-xs">{{ $fila['error'] }}</p>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="panel-playground-import-upload">
                    <span class="panel-playground-auth-preview-field"></span>
                    <p class="panel-muted mt-2 text-xs">{{ __('panel::panel.documentation.import_preview_direct') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-panel::playground-zona>
