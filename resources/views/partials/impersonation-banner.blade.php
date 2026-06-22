@php
    use Illuminate\Support\Str;
    use MyLaravelTools\Panel\Support\PanelAuth;

    $usuario = PanelAuth::user();
    $inicial = Str::upper(Str::substr((string) ($usuario?->name ?? '?'), 0, 1));
@endphp

<div
    class="panel-impersonation-sidebar"
    role="status"
    aria-live="polite"
    aria-label="{{ __('panel::panel.impersonate.banner', ['name' => $usuario?->name ?? '—', 'email' => $usuario?->email ?? '']) }}"
>
    <div class="panel-impersonation-sidebar-card">
        <span class="panel-impersonation-sidebar-glow" aria-hidden="true"></span>
        <span class="panel-impersonation-sidebar-shine" aria-hidden="true"></span>

        <div class="panel-impersonation-sidebar-body">
            <div class="panel-impersonation-sidebar-avatar" aria-hidden="true">
                <span class="panel-impersonation-sidebar-avatar-ring"></span>
                <span class="panel-impersonation-sidebar-avatar-letter">{{ $inicial }}</span>
            </div>

            <div class="panel-impersonation-sidebar-info">
                <span class="panel-impersonation-sidebar-name">{{ $usuario?->name ?? '—' }}</span>
                @if ($usuario?->email)
                    <span class="panel-impersonation-sidebar-email">{{ $usuario->email }}</span>
                @endif
                <span class="panel-impersonation-sidebar-hint">{{ __('panel::panel.impersonate.float_hint') }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('panel.impersonation.leave', [], false) }}" class="panel-impersonation-sidebar-form">
            @csrf
            <button type="submit" class="panel-impersonation-sidebar-exit" title="{{ __('panel::panel.impersonate.leave') }}">
                <x-panel::icon name="arrow-left" class="h-4 w-4 shrink-0" />
                <span>{{ __('panel::panel.impersonate.leave_short') }}</span>
            </button>
        </form>
    </div>
</div>
