<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Feature;

use Panel\Minimalist\Livewire\Auth\Login;
use Panel\Minimalist\Livewire\Auth\Register;
use Panel\Minimalist\Tests\Fixtures\PanelUser;
use Panel\Minimalist\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

final class AuthTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.providers.users.model', PanelUser::class);
        $app['config']->set('panel.auth.enabled', true);
        $app['config']->set('panel.auth.register', true);
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

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('panel.login'));
    }

    public function test_user_can_login_via_livewire(): void
    {
        PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@test.com')
            ->set('password', 'secret-password')
            ->call('login')
            ->assertRedirect(route('panel.dashboard'));

        $this->assertAuthenticatedAs(
            PanelUser::query()->where('email', 'admin@test.com')->first(),
        );
    }

    public function test_user_can_register_via_livewire(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'New User')
            ->set('email', 'new@test.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('panel.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'new@test.com',
            'name' => 'New User',
        ]);

        $this->assertAuthenticated();
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_dashboard(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->get('/admin/login')
            ->assertRedirect(route('panel.dashboard'));
    }

    public function test_logout_redirects_to_login(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user)
            ->post('/admin/logout')
            ->assertRedirect(route('panel.login'));

        $this->assertGuest();
    }
}
