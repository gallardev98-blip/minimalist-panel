<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Widgets\ChartWidget;
use MyLaravelTools\Panel\Widgets\ResourceCountWidget;
use MyLaravelTools\Panel\Widgets\StatWidget;

final class WidgetRegistry
{
    /** @return array<int, StatWidget|ResourceCountWidget|ChartWidget> */
    public function all(): array
    {
        return config('panel.widgets', []);
    }
}
