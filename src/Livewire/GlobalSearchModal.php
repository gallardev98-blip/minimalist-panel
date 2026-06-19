<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire;

use Panel\Minimalist\Support\GlobalSearch;
use Livewire\Attributes\On;
use Livewire\Component;

final class GlobalSearchModal extends Component
{
    public bool $open = false;

    public string $query = '';

    #[On('open-global-search')]
    public function openModal(): void
    {
        $this->open = true;
        $this->query = '';
        $this->dispatch('global-search-opened');
    }

    public function close(): void
    {
        $this->open = false;
        $this->query = '';
    }

    public function render(): mixed
    {
        $results = $this->open
            ? app(GlobalSearch::class)->search($this->query)
            : [];

        return view('panel::livewire.global-search-modal', [
            'results' => $results,
        ]);
    }
}
