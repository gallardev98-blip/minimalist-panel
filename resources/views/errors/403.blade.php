@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.403.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
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
