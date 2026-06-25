@extends('panel::layouts.error', ['titulo' => __('panel::panel.errors.422.title')])

@section('contenido')
    <svg class="panel-error-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
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
