<header class="panel-header">
    <div class="panel-header-inner">
        <div class="flex min-w-0 flex-1 items-center gap-3">
            <button
                type="button"
                class="panel-btn-icon shrink-0 lg:hidden"
                @click="sidebarOpen = !sidebarOpen"
                aria-label="{{ __('panel::panel.menu_open') }}"
            >
                <x-panel::icon name="menu" class="h-5 w-5" />
            </button>

            <div class="min-w-0 flex-1">
                @include('panel::partials.breadcrumbs')
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-2 sm:gap-3">
            @auth(config('panel.guard'))
                @php
                    $user = auth(config('panel.guard'))->user();
                    $initial = strtoupper(substr($user?->name ?? $user?->email ?? '?', 0, 1));
                @endphp
                <div class="hidden items-center gap-2 sm:flex">
                    <span class="panel-user-avatar">{{ $initial }}</span>
                    <span class="panel-text max-w-[10rem] truncate text-sm">{{ $user?->name ?? $user?->email }}</span>
                </div>
            @endauth
        </div>
    </div>
</header>
