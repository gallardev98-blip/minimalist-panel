@if (count($locales) > 1)
    <div class="panel-locale-switcher" x-data="{ open: false }" @click.outside="open = false">
        <button
            type="button"
            class="panel-btn-icon panel-sidebar-toolbar-btn"
            @click="open = !open"
            :aria-expanded="open"
            aria-haspopup="listbox"
            aria-label="{{ __('panel::panel.locale_selector') }}"
            title="{{ __('panel::panel.locale_selector') }}"
        >
            <x-panel::icon name="globe" class="h-4 w-4" />
        </button>

        <div x-show="open" x-cloak x-transition class="panel-locale-menu" role="listbox">
            @foreach ($locales as $code => $label)
                <button
                    type="button"
                    wire:click="setLocale('{{ $code }}')"
                    class="panel-locale-option {{ $current === $code ? 'panel-locale-option--active' : '' }}"
                    role="option"
                    aria-selected="{{ $current === $code ? 'true' : 'false' }}"
                    @click="open = false"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
@endif
