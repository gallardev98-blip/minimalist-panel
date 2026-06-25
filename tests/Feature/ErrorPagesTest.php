<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class ErrorPagesTest extends TestCase
{
    public static function codigosError(): array
    {
        return [
            ['403', '403'],
            ['404', '404'],
            ['419', '419'],
            ['422', '422'],
            ['429', '429'],
            ['500', '500'],
            ['503', '503'],
        ];
    }

    #[DataProvider('codigosError')]
    public function test_la_vista_de_error_se_renderiza_con_layout_y_codigo(string $vista, string $codigo): void
    {
        $html = view("panel::errors.{$vista}", ['exception' => null])->render();

        $this->assertStringContainsString($codigo, $html);
        $this->assertStringContainsString('panel-error-code', $html);
        $this->assertStringContainsString('panel-error-actions', $html);
        $this->assertStringContainsString('panel-error-theme-toggle', $html);
        $this->assertStringContainsString("--panel-font: '", $html);
        $this->assertStringNotContainsString('&#039;', $html);
        $this->assertStringNotContainsString('panel::panel.errors', $html);
        $this->assertStringNotContainsString('x-panel::icon', $html);
    }

    public function test_el_layout_es_minimal_sin_marca_ni_badge(): void
    {
        $html = view('panel::errors.404', ['exception' => null])->render();

        $this->assertStringNotContainsString('panel-error-brand', $html);
        $this->assertStringNotContainsString('panel-error-badge', $html);
        $this->assertStringNotContainsString('panel-error-card', $html);
    }

    public function test_la_vista_503_muestra_el_mensaje_de_mantenimiento_de_la_excepcion(): void
    {
        $excepcion = new \RuntimeException('Servicio temporalmente fuera');

        $html = view('panel::errors.503', ['exception' => $excepcion])->render();

        $this->assertStringContainsString('Servicio temporalmente fuera', $html);
    }

    public function test_la_vista_500_no_filtra_el_mensaje_interno_de_la_excepcion(): void
    {
        $excepcion = new \RuntimeException('Detalle interno sensible');

        $html = view('panel::errors.500', ['exception' => $excepcion])->render();

        $this->assertStringNotContainsString('Detalle interno sensible', $html);
    }

    public function test_el_layout_de_error_es_autocontenido_sin_vite_ni_livewire(): void
    {
        $html = view('panel::errors.404', ['exception' => null])->render();

        $this->assertStringNotContainsString('@vite', $html);
        $this->assertStringNotContainsString('livewireScripts', $html);
        $this->assertStringContainsString('--panel-bg', $html);
    }
}
