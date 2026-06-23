<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class InstallPanelSaasTest extends TestCase
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

    public function test_install_saas_genera_tenant_y_vistas(): void
    {
        $tenant = app_path('Models/Tenant.php');
        $resource = app_path('Panel/Resources/TenantResource.php');
        $widget = app_path('Panel/Widgets/TenantCountWidget.php');
        $vista = resource_path('views/panel/saas/campo-plan.blade.php');
        $this->archivos = [$tenant, $resource, $widget, $vista];

        $this->artisan('panel:install', ['--saas' => true, '--force' => true])
            ->assertSuccessful();

        $this->assertFileExists($tenant);
        $this->assertFileExists($resource);
        $this->assertFileExists($widget);
        $this->assertFileExists($vista);
        $this->assertStringContainsString('saas-plan', File::get(config_path('panel.php')));
    }
}
