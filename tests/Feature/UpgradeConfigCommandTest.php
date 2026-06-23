<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class UpgradeConfigCommandTest extends TestCase
{
    private ?string $configOriginal = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configOriginal = File::get(config_path('panel.php'));
    }

    protected function tearDown(): void
    {
        if ($this->configOriginal !== null) {
            File::put(config_path('panel.php'), $this->configOriginal);
        }

        foreach (glob(config_path('panel.php.bak.*')) ?: [] as $backup) {
            File::delete($backup);
        }

        parent::tearDown();
    }

    public function test_dry_run_sin_cambios_si_config_completa(): void
    {
        $this->artisan('panel:upgrade-config', ['--dry-run' => true])
            ->assertSuccessful();
    }

    public function test_fusiona_config_antigua(): void
    {
        File::put(config_path('panel.php'), "<?php\n\ndeclare(strict_types=1);\n\nreturn ['path' => 'legacy'];\n");

        $this->artisan('panel:upgrade-config', ['--force' => true])
            ->assertSuccessful();

        $config = require config_path('panel.php');
        $this->assertSame('legacy', $config['path']);
        $this->assertArrayHasKey('import', $config);
    }
}
