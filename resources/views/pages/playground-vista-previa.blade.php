@php
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $zonasModificadas = $zonasModificadas ?? [];
    $zonaResaltada = $zonaResaltada ?? null;
    $etiquetaModo = PlaygroundDemo::etiquetaModo($modo);
@endphp

<div class="panel-playground-preview">
    <x-panel::playground-zona zona="contenido" :$zonasModificadas :$zonaResaltada>
        <x-panel::page-header class="mb-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1>{{ __('panel::panel.breadcrumbs.dashboard') }}</h1>
                    <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.documentation.preview_subtitle', ['mode' => $modo]) }}</p>
                </div>
                <span class="panel-badge panel-badge-primary text-xs">{{ $etiquetaModo }}</span>
            </div>
        </x-panel::page-header>

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            @foreach ([__('panel::panel.documentation.sample_stat_a') => '128', __('panel::panel.documentation.sample_stat_b') => '42', __('panel::panel.documentation.sample_stat_c') => '96%'] as $titulo => $valor)
                <div class="panel-card p-4">
                    <p class="panel-muted text-xs font-medium uppercase tracking-wide">{{ $titulo }}</p>
                    <p class="panel-heading mt-2 text-2xl font-bold">{{ $valor }}</p>
                </div>
            @endforeach
        </div>
    </x-panel::playground-zona>

    <x-panel::playground-zona zona="tabla" :$zonasModificadas :$zonaResaltada>
        <div class="panel-card overflow-hidden">
            <div class="panel-border border-b px-4 py-3">
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.sample_table') }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="panel-table w-full {{ $clasesTabla }}">
                    <thead>
                        <tr>
                            <th class="panel-muted px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('panel::panel.documentation.col_name') }}</th>
                            <th class="panel-muted px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('panel::panel.documentation.col_status') }}</th>
                            <th class="panel-muted px-4 py-3 text-left text-xs font-semibold uppercase">{{ __('panel::panel.documentation.col_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (range(1, 5) as $fila)
                            <tr>
                                <td class="px-4 py-3 text-sm">{{ __('panel::panel.documentation.sample_row') }} {{ $fila }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <x-panel::playground-zona zona="acentos" :$zonasModificadas :$zonaResaltada class="panel-playground-zona--inline">
                                        <span class="panel-badge {{ $fila % 2 === 0 ? 'panel-badge-success' : 'panel-badge-muted' }}">
                                            {{ $fila % 2 === 0 ? __('panel::panel.documentation.active') : __('panel::panel.documentation.draft') }}
                                        </span>
                                    </x-panel::playground-zona>
                                </td>
                                <td class="panel-muted px-4 py-3 text-sm">{{ now()->subDays($fila)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-panel::playground-zona>

    @include('panel::partials.playground-view-widget-demo', compact('zonasModificadas', 'zonaResaltada'))

    @include('panel::partials.playground-auth-preview', [
        'marca' => $marca ?? config('panel.brand.name', 'Panel'),
        'zonasModificadas' => $zonasModificadas,
        'zonaResaltada' => $zonaResaltada,
    ])

    @include('panel::partials.playground-graficos-demo', [
        'widgetsGraficos' => $widgetsGraficos ?? [],
        'graficos' => $graficos ?? [],
        'revisionGraficos' => $revisionGraficos ?? 0,
        'zonasModificadas' => $zonasModificadas,
        'zonaResaltada' => $zonaResaltada,
    ])
</div>
