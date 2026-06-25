@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.419.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
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
