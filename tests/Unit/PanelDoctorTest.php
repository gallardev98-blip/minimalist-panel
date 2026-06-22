<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelDoctor;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelDoctorTest extends TestCase
{
    public function test_diagnostico_incluye_config_ok(): void
    {
        $filas = PanelDoctor::diagnosticar();
        $mensajes = array_column($filas, 'mensaje');

        $this->assertContains('config/panel.php presente', $mensajes);
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
}
