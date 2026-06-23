<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests;

use MyLaravelTools\Panel\PanelServiceProvider;
use MyLaravelTools\Panel\Support\PanelManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PanelServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        PanelManager::reiniciarDefiniciones();

        parent::setUp();

        Blade::directive('vite', fn (): string => '<?php /* vite disabled in tests */ ?>');

        $sobreescrituras = $this->app['config']->get('panel', []);
        $configHost = config_path('panel.php');
        File::ensureDirectoryExists(dirname($configHost));
        File::copy(dirname(__DIR__).'/config/panel.php', $configHost);
        $this->app['config']->set('panel', array_replace_recursive(require $configHost, $sobreescrituras));
        PanelManager::reiniciarDefiniciones();
        PanelManager::sincronizarConfigInicial();
        PanelManager::establecerContexto(PanelManager::idPorDefecto());
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
        $app['config']->set('panel.theme.colors.primary', '#000000');
        $app['config']->set('panel.theme.colors.primary_dark', '#ffffff');
        $app['config']->set('panel.path', 'admin');
    }
}
