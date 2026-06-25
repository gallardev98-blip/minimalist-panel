@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.429.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
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
