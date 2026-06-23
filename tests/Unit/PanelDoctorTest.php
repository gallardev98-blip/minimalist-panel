<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelDoctor;
use MyLaravelTools\Panel\Tests\TestCase;
use Illuminate\Support\Facades\File;

final class PanelDoctorTest extends TestCase
{
    public function test_diagnostico_incluye_config_ok(): void
    {
        $filas = PanelDoctor::diagnosticar();
        $mensajes = array_column($filas, 'mensaje');

        $this->assertContains('config/panel.php presente', $mensajes);
        $this->assertContains('panel_route() operativa', $mensajes);
    }

    public function test_diagnostico_detecta_playground(): void
    {
        config()->set('panel.documentation.enabled', true);

        $filas = PanelDoctor::diagnosticar();
        $playground = collect($filas)->first(
            fn (array $fila): bool => str_contains($fila['mensaje'], 'Playground')
        );

        $this->assertNotNull($playground);
        $this->assertSame('ok', $playground['nivel']);
    }

    public function test_diagnostico_avisa_config_desactualizada(): void
    {
        File::put(config_path('panel.php'), "<?php\n\ndeclare(strict_types=1);\n\nreturn ['path' => 'admin'];\n");

        $filas = PanelDoctor::diagnosticar();
        $aviso = collect($filas)->first(
            fn (array $fila): bool => str_contains($fila['mensaje'], 'upgrade-config')
        );

        $this->assertNotNull($aviso);
        $this->assertSame('warn', $aviso['nivel']);
    }

    public function test_diagnostico_modo_panel_unico(): void
    {
        config()->set('panel.panels', []);

        $filas = PanelDoctor::diagnosticar();
        $multi = collect($filas)->first(
            fn (array $fila): bool => str_contains($fila['mensaje'], 'panel único')
        );

        $this->assertNotNull($multi);
        $this->assertSame('ok', $multi['nivel']);
    }

    public function test_diagnostico_multi_panel_ok(): void
    {
        config()->set('panel.panels', [
            'admin' => ['path' => 'admin'],
            'cliente' => ['path' => 'cliente'],
        ]);

        $filas = PanelDoctor::diagnosticar();
        $multi = collect($filas)->first(
            fn (array $fila): bool => str_contains($fila['mensaje'], 'Multi-panel')
        );

        $this->assertNotNull($multi);
        $this->assertSame('ok', $multi['nivel']);
    }

    public function test_diagnostico_detecta_path_duplicado(): void
    {
        config()->set('panel.panels', [
            'admin' => ['path' => 'mismo'],
            'otro' => ['path' => 'mismo'],
        ]);

        $filas = PanelDoctor::diagnosticar();
        $error = collect($filas)->first(
            fn (array $fila): bool => str_contains($fila['mensaje'], 'Path duplicado')
        );

        $this->assertNotNull($error);
        $this->assertSame('error', $error['nivel']);
    }
}
