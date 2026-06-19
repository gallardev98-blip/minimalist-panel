<?php

declare(strict_types=1);

namespace Panel\Minimalist;

use Panel\Minimalist\Commands\InstallPanelCommand;
use Panel\Minimalist\Commands\MakePageCommand;
use Panel\Minimalist\Commands\MakePolicyCommand;
use Panel\Minimalist\Commands\MakeResourceCommand;
use Panel\Minimalist\Livewire\Auth\ForgotPassword;
use Panel\Minimalist\Livewire\Auth\Login as PanelLogin;
use Panel\Minimalist\Livewire\Auth\Register as PanelRegister;
use Panel\Minimalist\Livewire\Auth\ResetPassword;
use Panel\Minimalist\Livewire\Dashboard;
use Panel\Minimalist\Livewire\PanelPage;
use Panel\Minimalist\Livewire\GlobalSearchModal;
use Panel\Minimalist\Livewire\RelationPanel;
use Panel\Minimalist\Livewire\ResourceForm;
use Panel\Minimalist\Livewire\ResourceIndex;
use Panel\Minimalist\Livewire\ResourceShow;
use Panel\Minimalist\Support\CsvExporter;
use Panel\Minimalist\Support\ExcelExporter;
use Panel\Minimalist\Support\GlobalSearch;
use Panel\Minimalist\Support\PageRegistry;
use Panel\Minimalist\Support\PolicyRegistrar;
use Panel\Minimalist\Support\ResourceAuthorizer;
use Panel\Minimalist\Support\ResourceRegistry;
use Panel\Minimalist\Support\WidgetRegistry;
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
        $this->app->singleton(CsvExporter::class);
        $this->app->singleton(ExcelExporter::class);
        $this->app->singleton(GlobalSearch::class);
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerViews();
        $this->registerTranslations();
        $this->registerPagination();
        $this->registerLivewireComponents();
        $this->registerPolicies();
        $this->registerRoutes();
    }

    private function registerPolicies(): void
    {
        $this->app->make(PolicyRegistrar::class)->register();
    }

    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

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
            MakePolicyCommand::class,
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
        Livewire::component('panel.dashboard', Dashboard::class);
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
    }
}
