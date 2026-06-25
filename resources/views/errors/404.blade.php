@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.404.title')])

@section('contenido')
    <p class="panel-error-code">404</p>
    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.404.title') }}</h1>
    <p class="panel-error-desc">{{ __('panel::panel.errors.404.desc') }}</p>

    <div class="panel-error-actions">
        <a href="javascript:history.back()" class="panel-error-btn panel-error-btn-ghost">
            <x-panel::icon name="arrow-left" /> {{ __('panel::panel.back') }}
        </a>
        <a href="/{{ config('panel.path', 'admin') }}" class="panel-error-btn panel-error-btn-primary">
            <x-panel::icon name="layout-dashboard" /> {{ __('panel::panel.errors.go_panel') }}
        </a>
    </div>
@endsection
