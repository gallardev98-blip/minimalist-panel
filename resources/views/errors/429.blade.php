@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.429.title')])

@section('contenido')
    <p class="panel-error-code">429</p>
    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.429.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.429.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-primary">
            <x-panel::icon name="rotate-ccw" /> {{ __('panel::panel.errors.429.action') }}
        </button>
    </div>
@endsection
