@props(['label'])

<span {{ $attributes->merge(['class' => 'panel-auth-loading-text']) }} aria-live="polite">
    {{ $label }}<span class="panel-auth-dots" aria-hidden="true"></span>
</span>
