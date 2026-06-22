<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Widgets\ChartWidget;
use MyLaravelTools\Panel\Widgets\ResourceCountWidget;
use MyLaravelTools\Panel\Widgets\StatWidget;

final class WidgetRegistry
{
    /** @return array<int, StatWidget|ResourceCountWidget|ChartWidget|\MyLaravelTools\Panel\Widgets\ViewWidget|object> */
    public function all(): array
    {
        $config = config('panel.widgets', []);
        $registrados = app(PanelExtensions::class)->widgetsRegistrados();

        return array_merge($config, $registrados);
    }
}
