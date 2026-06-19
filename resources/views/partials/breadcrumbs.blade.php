@php
    $crumbs = \Panel\Minimalist\Support\Breadcrumbs::resolve();
@endphp

@if (count($crumbs) > 0)
    <nav class="panel-breadcrumbs" aria-label="Breadcrumb">
        <ol class="panel-breadcrumbs-list">
            @foreach ($crumbs as $crumb)
                <li class="panel-breadcrumbs-item">
                    @if (! $loop->first)
                        <span class="panel-breadcrumbs-separator panel-muted" aria-hidden="true">/</span>
                    @endif

                    @if ($crumb['url'])
                        <a href="{{ $crumb['url'] }}" class="panel-breadcrumbs-link panel-muted" wire:navigate wire:navigate.hover>
                            {{ $crumb['label'] }}
                        </a>
                    @else
                        <span class="panel-breadcrumbs-current panel-heading">{{ $crumb['label'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
