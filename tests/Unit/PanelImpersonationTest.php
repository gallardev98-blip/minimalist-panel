<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelImpersonation;
use MyLaravelTools\Panel\Tests\Fixtures\PanelUser;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

final class PanelImpersonationTest extends TestCase
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

    public function test_inicia_suplantacion_y_restaura_admin(): void
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

        $this->assertTrue(PanelImpersonation::start($editor));
        $this->assertTrue(PanelImpersonation::isActive());
        $this->assertSame($editor->getKey(), Auth::id());
        $this->assertSame($admin->getKey(), PanelImpersonation::impersonator()?->getKey());

        $this->assertTrue(PanelImpersonation::leave());
        $this->assertFalse(PanelImpersonation::isActive());
        $this->assertSame($admin->getKey(), Auth::id());
    }

    public function test_no_suplanta_a_si_mismo(): void
    {
        $admin = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.test',
            'password' => bcrypt('secret'),
        ]);

        Auth::login($admin);

        $this->assertFalse(PanelImpersonation::canImpersonate($admin));
        $this->assertFalse(PanelImpersonation::start($admin));
    }

    public function test_respeta_ids_excluidos(): void
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

        config(['panel.impersonation.exclude_ids' => [$editor->getKey()]]);

        Auth::login($admin);

        $this->assertFalse(PanelImpersonation::canImpersonate($editor));
    }
}
