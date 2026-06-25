@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.500.title')])

@section('contenido')
    <x-panel::icon name="wrench" class="panel-error-icon" />

    <p class="panel-error-code">500</p>
    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.500.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.500.desc') }}</p>

    <div class="panel-error-actions">
        <button type="button" onclick="window.location.reload()" class="panel-error-btn panel-error-btn-ghost">
            {{ __('panel::panel.errors.500.action') }}
        </button>
        <a href="/{{ config('panel.path', 'admin') }}" class="panel-error-btn panel-error-btn-primary">
            {{ __('panel::panel.errors.go_panel') }}
        </a>
    </div>
@endsection
