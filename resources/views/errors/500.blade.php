@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.500.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
    </svg>

    <p class="panel-error-code">500</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.500.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.500.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-ghost">
            {{ __('panel::panel.errors.500.action_retry') }}
        </button>
        <a href="/{{ config('panel.path', 'admin') }}" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.go_panel') }}
        </a>
    </div>
@endsection
