@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.404.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803M15.803 15.803L21 21M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5" />
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
