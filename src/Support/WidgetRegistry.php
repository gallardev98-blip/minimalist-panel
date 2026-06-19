<?php

declare(strict_types=1);

namespace Panel\Minimalist\Support;

use Panel\Minimalist\Widgets\ResourceCountWidget;
use Panel\Minimalist\Widgets\StatWidget;

final class WidgetRegistry
{
    /** @return array<int, StatWidget|ResourceCountWidget> */
    public function all(): array
    {
        return config('panel.widgets', []);
    }
}
