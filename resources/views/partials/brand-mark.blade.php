@php
    $brandLogo = config('panel.brand.logo');
    $logoUrl = null;

    if (is_string($brandLogo) && $brandLogo !== '') {
        $logoUrl = str_starts_with($brandLogo, 'http://')
            || str_starts_with($brandLogo, 'https://')
            || str_starts_with($brandLogo, '//')
            ? $brandLogo
            : asset($brandLogo);
    }
@endphp

<div @class(['panel-brand-mark', 'panel-brand-mark--custom' => $logoUrl !== null])>
    @if ($logoUrl !== null)
        <img src="{{ $logoUrl }}" alt="" class="panel-brand-logo" />
    @else
        <x-panel::icon name="layers" class="h-4 w-4" />
    @endif
</div>
