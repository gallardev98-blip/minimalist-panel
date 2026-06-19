@props(['class' => ''])

<div {{ $attributes->merge(['class' => trim('panel-page-header '.$class)]) }}>
    <button
        type="button"
        class="panel-btn-icon panel-page-header-menu lg:hidden"
        @click="sidebarOpen = !sidebarOpen"
        aria-label="{{ __('panel::panel.menu_open') }}"
    >
        <x-panel::icon name="menu" class="h-5 w-5" />
    </button>

    <div class="panel-page-header-start">
        {{ $slot }}
    </div>

    @include('panel::partials.breadcrumbs')
</div>
