@php
    use MyLaravelTools\Panel\Support\PlaygroundDemo;

    $permisosActivo = (bool) config('panel.permissions.enabled', false);
    $driver = (string) config('panel.permissions.driver', 'spatie');
    $acceso = (string) config('panel.permissions.panel_access', 'access panel');
    $items = PlaygroundDemo::menuPermisos();
@endphp

<x-panel::playground-zona zona="permisos" :$zonasModificadas :$zonaResaltada>
    <div class="panel-card overflow-hidden">
        <div class="panel-border flex flex-wrap items-center justify-between gap-2 border-b px-4 py-3">
            <div>
                <h2 class="panel-heading text-sm font-semibold">{{ __('panel::panel.documentation.perm_preview_title') }}</h2>
                <p class="panel-muted mt-0.5 text-xs">{{ __('panel::panel.documentation.perm_preview_desc') }}</p>
            </div>
            <span @class(['panel-badge text-xs', $permisosActivo ? 'panel-badge-success' : 'panel-badge-muted'])>
                {{ $permisosActivo ? strtoupper($driver) : __('panel::panel.documentation.perm_preview_off') }}
            </span>
        </div>

        <div class="p-4">
            @if ($permisosActivo)
                <p class="panel-muted mb-3 text-xs">{{ __('panel::panel.documentation.perm_preview_hint', ['perm' => $acceso]) }}</p>
            @endif
            <ul class="panel-playground-perm-menu space-y-1">
                @foreach ($items as $item)
                    <li @class([
                        'panel-playground-perm-item',
                        'panel-playground-perm-item--hidden' => $permisosActivo && ! $item['visible'],
                    ])>
                        <x-panel::icon :name="$item['visible'] || ! $permisosActivo ? 'check' : 'lock'" class="h-3.5 w-3.5 shrink-0" />
                        <span class="truncate text-sm">{{ $item['label'] }}</span>
                        @if ($permisosActivo && ! $item['visible'] && ! empty($item['permiso']))
                            <code class="panel-playground-perm-code">{{ $item['permiso'] }}</code>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-panel::playground-zona>
