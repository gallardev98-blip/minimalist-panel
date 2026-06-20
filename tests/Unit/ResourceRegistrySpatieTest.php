<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Resources\Spatie\PermissionResource;
use MyLaravelTools\Panel\Resources\Spatie\RoleResource;
use MyLaravelTools\Panel\Support\ResourceRegistry;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\Fixtures\HostRoleResource;
use MyLaravelTools\Panel\Tests\TestCase;

final class ResourceRegistrySpatieTest extends TestCase
{
    public function test_host_resource_overrides_built_in_when_slug_matches(): void
    {
        if (! class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->markTestSkipped('Spatie Permission is not installed.');
        }

        $this->app['config']->set('panel.permissions.enabled', true);
        $this->app['config']->set('panel.permissions.resources', true);
        $this->app['config']->set('panel.discovery.enabled', false);
        $this->app['config']->set('panel.resources', [HostRoleResource::class, ArticleResource::class]);

        $registry = new ResourceRegistry();

        $this->assertSame(HostRoleResource::class, $registry->findBySlug('roles'));
        $this->assertNotSame(RoleResource::class, $registry->findBySlug('roles'));
    }

    public function test_built_in_permission_resource_is_registered_when_enabled(): void
    {
        if (! class_exists(\Spatie\Permission\Models\Permission::class)) {
            $this->markTestSkipped('Spatie Permission is not installed.');
        }

        $this->app['config']->set('panel.permissions.enabled', true);
        $this->app['config']->set('panel.permissions.resources', true);
        $this->app['config']->set('panel.discovery.enabled', false);
        $this->app['config']->set('panel.resources', []);

        $registry = new ResourceRegistry();

        $this->assertSame(PermissionResource::class, $registry->findBySlug('permissions'));
        $this->assertSame(RoleResource::class, $registry->findBySlug('roles'));
    }
}
