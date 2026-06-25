@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.503.title')])

@section('contenido')
    <x-panel::icon name="alert-triangle" class="panel-error-icon" />

    <p class="panel-error-code">503</p>
    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ $exception?->getMessage() ?: __('panel::panel.errors.503.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.503.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.503.action') }}
        </button>
    </div>
@endsection
