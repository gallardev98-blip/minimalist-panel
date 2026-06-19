<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Support\SpatieResourceRegistrar;
use Panel\Minimalist\Tests\TestCase;

final class SpatieResourceRegistrarTest extends TestCase
{
    public function test_built_in_resources_are_disabled_when_permissions_are_off(): void
    {
        $this->app['config']->set('panel.permissions.enabled', false);
        $this->app['config']->set('panel.permissions.resources', true);

        $this->assertFalse(SpatieResourceRegistrar::shouldRegister());
        $this->assertSame([], SpatieResourceRegistrar::resources());
    }

    public function test_built_in_resources_are_disabled_when_resources_flag_is_off(): void
    {
        $this->app['config']->set('panel.permissions.enabled', true);
        $this->app['config']->set('panel.permissions.resources', false);

        $this->assertFalse(SpatieResourceRegistrar::shouldRegister());
        $this->assertSame([], SpatieResourceRegistrar::resources());
    }

    public function test_built_in_resources_require_spatie_models(): void
    {
        $this->app['config']->set('panel.permissions.enabled', true);
        $this->app['config']->set('panel.permissions.resources', true);

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->assertTrue(SpatieResourceRegistrar::shouldRegister());
            $this->assertCount(2, SpatieResourceRegistrar::resources());
        } else {
            $this->assertFalse(SpatieResourceRegistrar::shouldRegister());
            $this->assertSame([], SpatieResourceRegistrar::resources());
        }
    }
}
