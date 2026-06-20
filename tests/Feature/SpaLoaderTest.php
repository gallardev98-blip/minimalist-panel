<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\Fixtures\PanelUser;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

final class SpaLoaderTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.providers.users.model', PanelUser::class);
        $app['config']->set('panel.auth.enabled', true);
        $app['config']->set('panel.permissions.enabled', false);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function test_login_page_includes_spa_loader_for_auth_transition(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('id="panel-spa-loader"', false)
            ->assertSee('panel-spa-loader--fullscreen', false)
            ->assertSee('registerPanelSpaNavigation', false);
    }

    public function test_layout_includes_spa_loader_with_integer_percent_markup(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('id="panel-spa-loader"', false)
            ->assertSee('data-panel-loader-progress', false)
            ->assertSee('data-panel-loader-progressbar', false)
            ->assertSee('data-panel-loader-ring', false)
            ->assertSee('panel-spa-loader-percent', false)
            ->assertSee('0%', false)
            ->assertSee('role="progressbar"', false)
            ->assertSee('aria-valuemin="0"', false)
            ->assertSee('aria-valuemax="100"', false)
            ->assertSee('aria-valuenow="0"', false);
    }

    public function test_spa_navigation_script_updates_integer_loader_progress(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('function setProgress', false)
            ->assertSee('Math.floor', false)
            ->assertSee('PROGRESS_CAP', false)
            ->assertSee('finishProgress', false)
            ->assertSee('event.detail?.cached', false);
    }

    public function test_dashboard_uses_page_header_with_breadcrumbs_row(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('panel-page-header', false)
            ->assertSee('panel-breadcrumbs', false);
    }
}
