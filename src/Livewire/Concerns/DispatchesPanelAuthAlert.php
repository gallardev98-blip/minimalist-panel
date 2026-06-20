<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire\Concerns;

use MyLaravelTools\Panel\Support\PanelIntegrations;
use Illuminate\Validation\ValidationException;

trait DispatchesPanelAuthAlert
{
    protected function alertAuthFailure(string $message): void
    {
        if (PanelIntegrations::alertasEnabled()) {
            $this->dispatch('alerta', titulo: $message, icono: 'error', toast: true);

            return;
        }

        throw ValidationException::withMessages([
            'email' => $message,
        ]);
    }
}
