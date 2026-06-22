<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Feature;

use MyLaravelTools\Panel\Tests\TestCase;

final class DoctorPanelCommandTest extends TestCase
{
    public function test_panel_doctor_ejecuta_sin_errores(): void
    {
        $this->artisan('panel:doctor')
            ->assertSuccessful();
    }
}
