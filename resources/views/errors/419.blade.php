@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.419.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
    </svg>

    <p class="panel-error-code">419</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.419.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.419.desc') }}</p>

    <div class="panel-error-actions">
        <a href="{{ url()->previous('/') }}" class="panel-error-btn panel-error-btn-ghost">
            &larr; {{ __('panel::panel.back') }}
        </a>
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.419.action') }}
        </button>
    </div>
@endsection
