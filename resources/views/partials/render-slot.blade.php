@php
    $vista = app(\MyLaravelTools\Panel\Support\PanelSlots::class)->vista($nombre);
@endphp

@if ($vista)
    @include($vista)
@endif
