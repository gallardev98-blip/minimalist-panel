<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Livewire\Profile;
use MyLaravelTools\Panel\Tests\Fixtures\PanelUser;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

final class ProfileTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.providers.users.model', PanelUser::class);
        $app['config']->set('panel.profile.enabled', true);
        $app['config']->set('panel.locale', 'es');
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

    public function test_guest_is_redirected_from_profile(): void
    {
        $this->get('/admin/profile')
            ->assertRedirect(panel_route('login'));
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        $this->actingAs($user)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('Mi perfil');
    }

    public function test_user_can_update_name_and_email(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('secret-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('name', 'Alberto')
            ->set('email', 'alberto@test.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'alberto@test.com',
            'name' => 'Alberto',
        ]);
    }

    public function test_user_can_update_password_with_current_password(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('current_password', 'old-password')
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('save')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('old-password'),
        ]);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('save')
            ->assertHasErrors(['current_password']);
    }
}
