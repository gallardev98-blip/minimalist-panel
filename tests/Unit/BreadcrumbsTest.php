<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Support\Breadcrumbs;
use Panel\Minimalist\Tests\Fixtures\ArticleResource;
use Panel\Minimalist\Tests\TestCase;
use Illuminate\Support\Facades\Route;

final class BreadcrumbsTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);
        $app['config']->set('panel.resources', [ArticleResource::class]);
    }

    protected function defineRoutes($router): void
    {
        $router->get('/admin', fn () => '')->name('panel.dashboard');
        $router->get('/admin/{resource}', fn () => '')->name('panel.resources.index');
        $router->get('/admin/{resource}/create', fn () => '')->name('panel.resources.create');
    }

    public function test_it_builds_dashboard_crumbs(): void
    {
        $request = $this->makeRequest('/admin');

        $crumbs = Breadcrumbs::resolve($request);

        $this->assertCount(1, $crumbs);
        $this->assertSame(__('panel::panel.breadcrumbs.dashboard'), $crumbs[0]['label']);
        $this->assertNull($crumbs[0]['url']);
    }

    public function test_it_builds_resource_index_crumbs(): void
    {
        $request = $this->makeRequest('/admin/articles');

        $crumbs = Breadcrumbs::resolve($request);

        $this->assertCount(2, $crumbs);
        $this->assertSame('Articles', $crumbs[1]['label']);
        $this->assertNull($crumbs[1]['url']);
    }

    public function test_it_builds_create_crumbs(): void
    {
        $request = $this->makeRequest('/admin/articles/create');

        $crumbs = Breadcrumbs::resolve($request);

        $this->assertSame(__('panel::panel.breadcrumbs.create'), $crumbs[2]['label']);
    }

    public function test_it_builds_page_crumbs(): void
    {
        config([
            'panel.pages.registered' => [\Panel\Minimalist\Tests\Fixtures\SettingsPage::class],
            'panel.pages.discovery.enabled' => false,
        ]);

        $this->app['router']->get('/admin/pages/{page}', fn () => '')->name('panel.pages.show');

        $request = $this->makeRequest('/admin/pages/settings');
        $crumbs = Breadcrumbs::resolve($request);

        $this->assertCount(2, $crumbs);
        $this->assertSame('Settings', $crumbs[1]['label']);
        $this->assertNull($crumbs[1]['url']);
    }

    private function makeRequest(string $uri): \Illuminate\Http\Request
    {
        $request = \Illuminate\Http\Request::create($uri, 'GET');
        $route = Route::getRoutes()->match($request);
        $request->setRouteResolver(fn () => $route);

        return $request;
    }
}
