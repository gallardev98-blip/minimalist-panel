<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Support\PanelImpersonation;
use MyLaravelTools\Panel\Tests\Fixtures\PanelUser;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

final class ImpersonationLeaveTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.providers.users.model', PanelUser::class);
        $app['config']->set('panel.impersonation.enabled', true);
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

    public function test_salir_suplantacion_redirige_al_dashboard(): void
    {
        $admin = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('secret'),
        ]);

        $editor = PanelUser::query()->create([
            'name' => 'Editor',
            'email' => 'editor@test.test',
            'password' => bcrypt('secret'),
        ]);

        Auth::login($admin);
        PanelImpersonation::start($editor);

        $this->post('/admin/impersonation/leave')
            ->assertRedirect('/admin');

        $this->assertFalse(PanelImpersonation::isActive());
        $this->assertSame($admin->getKey(), Auth::id());
    }
}
