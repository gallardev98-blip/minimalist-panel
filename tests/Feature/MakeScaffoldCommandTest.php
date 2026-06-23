<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class MakeScaffoldCommandTest extends TestCase
{
    /** @var list<string> */
    private array $archivos = [];

    private ?string $configOriginal = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configOriginal = File::get(config_path('panel.php'));
    }

    protected function tearDown(): void
    {
        foreach ($this->archivos as $ruta) {
            if (is_file($ruta)) {
                File::delete($ruta);
            }
        }

        if ($this->configOriginal !== null) {
            File::put(config_path('panel.php'), $this->configOriginal);
        }

        parent::tearDown();
    }

    public function test_scaffold_crea_resource_y_policy(): void
    {
        $resource = app_path('Panel/Resources/ArticuloResource.php');
        $policy = app_path('Policies/ArticuloPolicy.php');
        $this->archivos = [$resource, $policy];

        $this->artisan('panel:scaffold', [
            'name' => 'Articulo',
            '--policy' => true,
        ])->assertSuccessful();

        $this->assertFileExists($resource);
        $this->assertFileExists($policy);
        $this->assertStringContainsString('ArticuloResource', File::get($resource));
    }

    public function test_scaffold_crea_widget_stat(): void
    {
        $resource = app_path('Panel/Resources/TareaResource.php');
        $widget = app_path('Panel/Widgets/TareaWidget.php');
        $this->archivos = [$resource, $widget];

        $this->artisan('panel:scaffold', [
            'name' => 'Tarea',
            '--widget' => 'stat',
        ])->assertSuccessful();

        $this->assertFileExists($widget);
        $this->assertStringContainsString('StatWidget::make', File::get($widget));
    }
}
