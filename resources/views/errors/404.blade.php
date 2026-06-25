@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.404.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/><path d="M11 8v6"/>
    </svg>

    <p class="panel-error-code">404</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.404.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.404.desc') }}</p>

    <div class="panel-error-actions">
        <a href="javascript:history.back()" class="panel-error-btn panel-error-btn-ghost">
            &larr; {{ __('panel::panel.back') }}
        </a>
        <a href="/{{ config('panel.path', 'admin') }}" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.go_panel') }}
        </a>
    </div>
@endsection
