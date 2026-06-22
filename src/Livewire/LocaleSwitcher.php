<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire;

use MyLaravelTools\Panel\Support\PanelLocale;
use Livewire\Component;

final class LocaleSwitcher extends Component
{
    public string $menuPlacement = 'up';

    public function setLocale(string $locale): void
    {
        PanelLocale::set($locale);
        PanelLocale::apply();
        $this->redirect(request()->header('Referer', route('panel.dashboard', [], false)), navigate: true);
    }

    public function render(): mixed
    {
        return view('panel::livewire.locale-switcher', [
            'locales' => PanelLocale::available(),
            'current' => PanelLocale::resolve() ?? app()->getLocale(),
            'menuPlacement' => in_array($this->menuPlacement, ['up', 'down'], true) ? $this->menuPlacement : 'up',
        ]);
    }
}
