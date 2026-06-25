@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.422.title')])

@section('contenido')
    <p class="panel-error-code">422</p>
    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.422.title') }}</h1>
    <p class="panel-error-desc">{{ $exception?->getMessage() ?: __('panel::panel.errors.422.desc') }}</p>

    <div class="panel-error-actions">
        <a href="javascript:history.back()" class="panel-error-btn panel-error-btn-primary">
            <x-panel::icon name="arrow-left" /> {{ __('panel::panel.back') }}
        </a>
    </div>
@endsection
