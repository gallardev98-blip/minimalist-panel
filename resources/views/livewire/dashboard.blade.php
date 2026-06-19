<div>
    <x-panel::page-header class="mb-8">
        <h1>{{ __('panel::panel.breadcrumbs.dashboard') }}</h1>
        <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.welcome') }}</p>
    </x-panel::page-header>

    @if ($widgets !== [])
        <section class="mb-10">
            <h2 class="panel-section-title mb-4">{{ __('panel::panel.overview') }}</h2>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($widgets as $widget)
                    @include('panel::partials.widget-card', ['widget' => $widget])
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <h2 class="panel-section-title mb-4">{{ __('panel::panel.resources') }}</h2>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($resourceLinks as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="panel-resource-card group block"
                    wire:navigate
                    wire:navigate.hover
                >
                    <div class="panel-resource-icon mb-3">
                        <x-panel::icon :name="$item['icon'] ?? 'layers'" class="h-5 w-5" />
                    </div>
                    <h3 class="panel-heading font-semibold group-hover:text-[rgb(var(--panel-primary))]">{{ $item['label'] }}</h3>
                    <p class="panel-muted mt-1 text-sm">{{ __('panel::panel.manage', ['label' => strtolower($item['label'])]) }}</p>
                </a>
            @empty
                <div class="panel-empty-state col-span-full">
                    <x-panel::icon name="layers" class="panel-muted mx-auto mb-3 h-8 w-8" />
                    <p class="panel-muted">{{ __('panel::panel.no_resources') }}</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
