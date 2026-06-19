<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire\Concerns;

trait DispatchesPanelToasts
{
    protected function toast(string $message, string $type = 'success'): void
    {
        $this->dispatch('panel-toast', type: $type, message: $message);
    }

    protected function toastSuccess(string $message): void
    {
        $this->toast($message, 'success');
    }

    protected function toastError(string $message): void
    {
        $this->toast($message, 'error');
    }
}
