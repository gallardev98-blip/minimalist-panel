<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests;

use MyLaravelTools\Panel\PanelServiceProvider;
use Illuminate\Support\Facades\Blade;
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
        parent::setUp();

        Blade::directive('vite', fn (): string => '<?php /* vite disabled in tests */ ?>');
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
        $app['config']->set('panel.theme.colors.primary', '#000000');
        $app['config']->set('panel.theme.colors.primary_dark', '#ffffff');
        $app['config']->set('panel.path', 'admin');
    }
}
