<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Pages\Page;
use Panel\Minimalist\Support\NavigationBuilder;
use Panel\Minimalist\Support\PageRegistry;
use Panel\Minimalist\Support\PanelPermission;
use Panel\Minimalist\Tests\Fixtures\SettingsPage;
use Panel\Minimalist\Tests\TestCase;

final class PageTest extends TestCase
{
    public function test_page_slug_and_url(): void
    {
        $this->assertSame('settings', SettingsPage::slug());
        $this->assertSame('Settings', SettingsPage::label());
        $this->assertStringContainsString('/admin/pages/settings', SettingsPage::url());
    }

    public function test_page_registry_finds_by_slug(): void
    {
        config([
            'panel.pages.registered' => [SettingsPage::class],
            'panel.pages.discovery.enabled' => false,
        ]);

        $registry = $this->app->make(PageRegistry::class);

        $this->assertSame(SettingsPage::class, $registry->findBySlug('settings'));
        $this->assertNull($registry->findBySlug('missing'));
    }

    public function test_navigation_includes_page_links(): void
    {
        config([
            'panel.pages.registered' => [SettingsPage::class],
            'panel.pages.discovery.enabled' => false,
            'panel.navigation' => [
                ['page' => SettingsPage::class],
            ],
        ]);

        $navigation = app(\Panel\Minimalist\Support\ResourceRegistry::class)->navigation();

        $this->assertCount(1, $navigation);
        $this->assertSame('Settings', $navigation[0]['label']);
        $this->assertStringContainsString('/pages/settings', $navigation[0]['url']);
    }

    public function test_navigation_filters_pages_without_access(): void
    {
        config([
            'panel.permissions.enabled' => true,
            'panel.pages.registered' => [SettingsPage::class],
            'panel.pages.discovery.enabled' => false,
            'panel.navigation' => [
                ['page' => SettingsPage::class],
            ],
        ]);

        $this->assertFalse(SettingsPage::canAccess());

        $navigation = app(\Panel\Minimalist\Support\ResourceRegistry::class)->navigation();

        $this->assertSame([], $navigation);
    }

    public function test_panel_permission_disabled_allows_access(): void
    {
        config(['panel.permissions.enabled' => false]);

        $this->assertTrue(PanelPermission::check('any-permission'));
        $this->assertTrue(SettingsPage::canAccess());
    }
}
