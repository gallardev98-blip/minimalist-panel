@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.503.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
    </svg>

    <p class="panel-error-code">503</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ $exception?->getMessage() ?: __('panel::panel.errors.503.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.503.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.503.action') }}
        </button>
    </div>
@endsection
