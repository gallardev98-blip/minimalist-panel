@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.403.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>

    <p class="panel-error-code">403</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.403.title') }}</h1>
    <p class="panel-error-desc">{{ $exception?->getMessage() ?: __('panel::panel.errors.403.desc') }}</p>

    <div class="panel-error-actions">
        <a href="javascript:history.back()" class="panel-error-btn panel-error-btn-ghost">
            &larr; {{ __('panel::panel.back') }}
        </a>
        <a href="/{{ config('panel.path', 'admin') }}" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.go_panel') }}
        </a>
    </div>
@endsection
