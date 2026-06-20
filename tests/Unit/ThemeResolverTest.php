<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\ThemeResolver;
use MyLaravelTools\Panel\Tests\TestCase;

final class ThemeResolverTest extends TestCase
{
    public function test_it_resolves_monochrome_primary_variables(): void
    {
        $light = ThemeResolver::lightVariables();

        $this->assertSame('0 0 0', $light['panel-primary']);
        $this->assertSame('255 255 255', $light['panel-primary-fg']);
    }

    public function test_it_resolves_dark_primary_variables(): void
    {
        $dark = ThemeResolver::darkVariables();

        $this->assertSame('255 255 255', $dark['panel-primary']);
        $this->assertSame('0 0 0', $dark['panel-primary-fg']);
    }

    public function test_it_resolves_chart_colors_from_theme(): void
    {
        $colors = ThemeResolver::chartColors(['success', 'danger']);

        $this->assertSame('rgb(22, 163, 74)', $colors[0]);
        $this->assertSame('rgb(220, 38, 38)', $colors[1]);
        $this->assertSame(['success', 'danger'], ThemeResolver::defaultChartColorKeys('doughnut', 2));
    }
}
