@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.422.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>
    </svg>

    <p class="panel-error-code">422</p>

    <div class="panel-error-divider"></div>

    <h1 class="panel-error-title">{{ __('panel::panel.errors.422.title') }}</h1>
    <p class="panel-error-desc">{{ $exception?->getMessage() ?: __('panel::panel.errors.422.desc') }}</p>

    <div class="panel-error-actions">
        <a href="javascript:history.back()" class="panel-error-btn panel-error-btn-primary">
            &larr; {{ __('panel::panel.back') }}
        </a>
    </div>
@endsection
