<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Support\PanelRelacionesGuia;
use MyLaravelTools\Panel\Tests\TestCase;

final class PanelRelacionesGuiaTest extends TestCase
{
    public function test_pasos_incluyen_tres_tipos(): void
    {
        $pasos = PanelRelacionesGuia::pasos();

        $this->assertCount(3, $pasos);
        $this->assertSame(['hasOne', 'morphMany', 'belongsToMany'], array_column($pasos, 'id'));
    }

    public function test_codigo_incluye_relation_manager(): void
    {
        foreach (PanelRelacionesGuia::pasos() as $paso) {
            $this->assertStringContainsString('RelationManager', $paso['codigo']);
        }
    }
}
