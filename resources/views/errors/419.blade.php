@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.419.title')])

@section('contenido')
    <x-panel::icon name="clock" class="panel-error-icon" />

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
