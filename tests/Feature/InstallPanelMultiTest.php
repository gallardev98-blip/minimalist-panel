<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class InstallPanelMultiTest extends TestCase
{
    private ?string $configOriginal = null;

    /** @var list<string> */
    private array $archivos = [];

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

    public function test_install_multi_genera_paneles(): void
    {
        $admin = config_path('panel-admin.php');
        $cliente = config_path('panel-cliente.php');
        $this->archivos = [$admin, $cliente];

        $this->artisan('panel:install', ['--multi' => true, '--force' => true])
            ->assertSuccessful();

        $this->assertFileExists($admin);
        $this->assertFileExists($cliente);

        $raiz = require config_path('panel.php');
        $this->assertIsArray($raiz['panels'] ?? null);
        $this->assertArrayHasKey('admin', $raiz['panels']);
        $this->assertArrayHasKey('cliente', $raiz['panels']);
    }
}
