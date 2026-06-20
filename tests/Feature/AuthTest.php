<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Livewire\Auth\ForgotPassword;
use MyLaravelTools\Panel\Livewire\Auth\Login;
use MyLaravelTools\Panel\Livewire\Auth\Register;
use MyLaravelTools\Panel\Livewire\Auth\ResetPassword;
use MyLaravelTools\Panel\Support\PanelAuthMessages;
use MyLaravelTools\Panel\Tests\Fixtures\PanelUser;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;

final class AuthTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('auth.providers.users.model', PanelUser::class);
        $app['config']->set('panel.auth.enabled', true);
        $app['config']->set('panel.auth.register', true);
        $app['config']->set('panel.auth.password_reset', true);
        $app['config']->set('panel.locale', 'es');
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

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get('/admin')
            ->assertRedirect(route('panel.login'));
    }

    public function test_invalid_login_does_not_redirect(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'wrong@test.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertNoRedirect();
    }

    public function test_login_validation_messages_use_panel_locale(): void
    {
        Livewire::test(Login::class)
            ->call('login')
            ->assertHasErrors(['email', 'password'])
            ->assertSee(__('panel::panel.validation.required', [
                'attribute' => __('panel::panel.auth.email'),
            ]), false);
    }

    public function test_invalid_login_shows_validation_error_when_alertas_is_unavailable(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'wrong@test.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_invalid_login_does_not_duplicate_error_message_in_markup(): void
    {
        $component = Livewire::test(Login::class)
            ->set('email', 'wrong@test.com')
            ->set('password', 'wrong-password')
            ->call('login');

        $message = __('panel::panel.auth.failed');
        $occurrences = substr_count($component->html(), $message);

        $this->assertSame(1, $occurrences);
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

    public function test_forgot_password_sends_notification(): void
    {
        Notification::fake();

        $user = PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'admin@test.com')
            ->call('sendResetLink')
            ->assertSet('statusMessage', PanelAuthMessages::passwordStatus(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_password_updates_credentials(): void
    {
        PanelUser::query()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken(
            PanelUser::query()->where('email', 'admin@test.com')->first(),
        );

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'admin@test.com')
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('resetPassword')
            ->assertRedirect(route('panel.login'));

        $user = PanelUser::query()->where('email', 'admin@test.com')->first();

        $this->assertTrue(Hash::check('new-password-123', $user->password));
    }

    public function test_forgot_password_unknown_email_shows_spanish_error(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'missing@test.com')
            ->call('sendResetLink')
            ->assertHasErrors(['email'])
            ->assertSee('No encontramos ningún usuario con ese correo electrónico');
    }

    public function test_forgot_password_page_is_available(): void
    {
        $this->get('/admin/forgot-password')->assertOk();
    }
}
