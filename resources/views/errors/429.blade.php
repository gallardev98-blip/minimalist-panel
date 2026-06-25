@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.429.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>
    </svg>

    <p class="panel-error-code">429</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.429.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.429.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.429.action') }}
        </button>
    </div>
@endsection
