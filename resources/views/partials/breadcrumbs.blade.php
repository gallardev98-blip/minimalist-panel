@php
    $crumbs = \Panel\Minimalist\Support\Breadcrumbs::resolve();
@endphp

@if (count($crumbs) > 0)
    <nav class="panel-breadcrumbs" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm">
            @foreach ($crumbs as $crumb)
                <li class="flex items-center gap-1.5">
                    @if (! $loop->first)
                        <span class="panel-muted" aria-hidden="true">/</span>
                    @endif

                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="panel-muted hover:panel-heading transition" wire:navigate wire:navigate.hover>
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span class="panel-heading font-medium">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
