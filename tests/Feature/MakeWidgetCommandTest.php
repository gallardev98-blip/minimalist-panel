<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class MakeWidgetCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        $ruta = app_path('Panel/Widgets/DemoStatWidget.php');
        if (is_file($ruta)) {
            File::delete($ruta);
        }

        parent::tearDown();
    }

    public function test_make_widget_crea_clase_stat(): void
    {
        $this->artisan('panel:make-widget', ['name' => 'DemoStat', '--type' => 'stat'])
            ->assertSuccessful();

        $ruta = app_path('Panel/Widgets/DemoStatWidget.php');
        $this->assertFileExists($ruta);
        $this->assertStringContainsString('StatWidget::make', File::get($ruta));
    }
}
