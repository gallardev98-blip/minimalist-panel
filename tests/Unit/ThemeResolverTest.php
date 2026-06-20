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
}
