<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelExtensionesGuia;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelExtensionesGuiaTest extends TestCase
{
    public function test_define_tres_pasos(): void
    {
        $pasos = PanelExtensionesGuia::pasos();

        $this->assertCount(3, $pasos);
        $this->assertSame(['campo', 'columna', 'widget'], array_column($pasos, 'id'));
    }

    public function test_pasos_incluyen_codigo(): void
    {
        foreach (PanelExtensionesGuia::pasos() as $paso) {
            $this->assertNotSame('', trim($paso['codigo']));
            $this->assertNotSame('', $paso['titulo']);
        }
    }
}
