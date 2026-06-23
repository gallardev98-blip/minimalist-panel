<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class InstallPanelStarterTest extends TestCase
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

    public function test_install_starter_genera_post_y_widget(): void
    {
        $post = app_path('Models/Post.php');
        $resource = app_path('Panel/Resources/PostResource.php');
        $widget = app_path('Panel/Widgets/PostCountWidget.php');
        $this->archivos = [$post, $resource, $widget];

        $this->artisan('panel:install', ['--starter' => true, '--force' => true])
            ->assertSuccessful();

        $this->assertFileExists($post);
        $this->assertFileExists($resource);
        $this->assertFileExists($widget);
        $this->assertStringContainsString('PostCountWidget', File::get(config_path('panel.php')));
    }
}
