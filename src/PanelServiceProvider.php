<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel;

use MyLaravelTools\Panel\Commands\InstallPanelCommand;
use MyLaravelTools\Panel\Commands\DoctorPanelCommand;
use MyLaravelTools\Panel\Commands\MakePageCommand;
use MyLaravelTools\Panel\Commands\MakeWidgetCommand;
use MyLaravelTools\Panel\Commands\MakePolicyCommand;
use MyLaravelTools\Panel\Commands\MakeResourceCommand;
use MyLaravelTools\Panel\Commands\UpgradeViewsCommand;
use MyLaravelTools\Panel\Livewire\Auth\ForgotPassword;
use MyLaravelTools\Panel\Livewire\Auth\Login as PanelLogin;
use MyLaravelTools\Panel\Livewire\Auth\Register as PanelRegister;
use MyLaravelTools\Panel\Livewire\Auth\ResetPassword;
use MyLaravelTools\Panel\Livewire\Auth\VerifyEmail;
use MyLaravelTools\Panel\Livewire\PlaygroundApp;
use MyLaravelTools\Panel\Livewire\LocaleSwitcher;
use MyLaravelTools\Panel\Livewire\PanelPage;
use MyLaravelTools\Panel\Livewire\Profile;
use MyLaravelTools\Panel\Livewire\GlobalSearchModal;
use MyLaravelTools\Panel\Livewire\RelationPanel;
use MyLaravelTools\Panel\Livewire\ResourceForm;
use MyLaravelTools\Panel\Livewire\ResourceIndex;
use MyLaravelTools\Panel\Livewire\ResourceShow;
use MyLaravelTools\Panel\Support\PanelExtensions;
use MyLaravelTools\Panel\Support\PanelSlots;
use MyLaravelTools\Panel\Support\CsvExporter;
use MyLaravelTools\Panel\Support\PanelLocale;
use MyLaravelTools\Panel\Support\ExcelExporter;
use MyLaravelTools\Panel\Support\GlobalSearch;
use MyLaravelTools\Panel\Support\ImportTemplateExporter;
use MyLaravelTools\Panel\Support\PageRegistry;
use MyLaravelTools\Panel\Support\PolicyRegistrar;
use MyLaravelTools\Panel\Support\ResourceAuthorizer;
use MyLaravelTools\Panel\Support\ResourceImporter;
use MyLaravelTools\Panel\Support\ResourceRegistry;
use MyLaravelTools\Panel\Support\WidgetRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class PanelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/panel.php', 'panel');

        $this->app->singleton(ResourceRegistry::class);
        $this->app->singleton(PageRegistry::class);
        $this->app->singleton(ResourceAuthorizer::class);
        $this->app->singleton(WidgetRegistry::class);
        $this->app->singleton(PanelExtensions::class);
        $this->app->singleton(PanelSlots::class);
        $this->app->singleton(CsvExporter::class);
        $this->app->singleton(ExcelExporter::class);
        $this->app->singleton(ResourceImporter::class);
        $this->app->singleton(ImportTemplateExporter::class);
        $this->app->singleton(GlobalSearch::class);
    }

    public function boot(): void
    {
        $this->registerExtensions();
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerPagination();
        $this->registerLivewireComponents();
        $this->registerPanelLocaleForLivewire();
        $this->registerPolicies();
        $this->registerRoutes();
    }

    private function registerPanelLocaleForLivewire(): void
    {
        Livewire::listen('component.booted', function (object $component): void {
            if (! str_starts_with($component::class, 'MyLaravelTools\\Panel\\')) {
                return;
            }

            PanelLocale::apply();
        });
    }

    private function registerPolicies(): void
    {
        $this->app->make(PolicyRegistrar::class)->register();
    }

    private function registerExtensions(): void
    {
        $this->app->make(PanelExtensions::class)->aplicarDesdeConfig();
        $this->app->make(PanelSlots::class)->aplicarDesdeConfig();
    }

    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../documentation/panel' => base_path('documentation/panel'),
        ], 'panel-documentation');

        $this->publishes([
            __DIR__ . '/../config/panel.php' => config_path('panel.php'),
        ], 'panel-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/panel'),
        ], 'panel-views');

        $this->publishes([
            __DIR__ . '/../resources/css/panel.css' => resource_path('css/panel.css'),
        ], 'panel-assets');

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/panel'),
        ], 'panel-stubs');
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallPanelCommand::class,
            MakeResourceCommand::class,
            MakePageCommand::class,
            MakeWidgetCommand::class,
            MakePolicyCommand::class,
            UpgradeViewsCommand::class,
            DoctorPanelCommand::class,
        ]);
    }

    private function registerTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'panel');
    }

    private function registerPagination(): void
    {
        Paginator::defaultView('panel::partials.pagination');
        Paginator::defaultSimpleView('panel::partials.pagination');
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'panel');

        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'panel');
    }

    private function registerLivewireComponents(): void
    {
        Livewire::component('panel.global-search', GlobalSearchModal::class);
        Livewire::component('panel.login', PanelLogin::class);
        Livewire::component('panel.register', PanelRegister::class);
        Livewire::component('panel.forgot-password', ForgotPassword::class);
        Livewire::component('panel.reset-password', ResetPassword::class);
        Livewire::component('panel.verify-email', VerifyEmail::class);
        Livewire::component('panel.playground', PlaygroundApp::class);
        Livewire::component('panel.locale-switcher', LocaleSwitcher::class);
        Livewire::component('panel.dashboard', Dashboard::class);
        Livewire::component('panel.profile', Profile::class);
        Livewire::component('panel.page', PanelPage::class);
        Livewire::component('panel.resource-index', ResourceIndex::class);
        Livewire::component('panel.resource-form', ResourceForm::class);
        Livewire::component('panel.resource-show', ResourceShow::class);
        Livewire::component('panel.relation-panel', RelationPanel::class);
    }

    private function registerRoutes(): void
    {
        $prefix = config('panel.path', 'admin');

        Route::group([
            'prefix' => $prefix,
            'as' => 'panel.',
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/panel-auth.php');
        });

        Route::group([
            'prefix' => $prefix,
            'middleware' => config('panel.middleware', ['web']),
            'as' => 'panel.',
        ], function (): void {
            $this->loadRoutesFrom(__DIR__ . '/../routes/panel.php');
        });

        if (config('panel.documentation.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/playground.php');
        }
    }
}
