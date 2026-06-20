<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Livewire;

use MyLaravelTools\Panel\Livewire\Concerns\InteractsWithPanelResource;
use MyLaravelTools\Panel\Support\NavigationBuilder;
use MyLaravelTools\Panel\Support\ResourceRegistry;
use MyLaravelTools\Panel\Support\WidgetRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('panel::layouts.app')]
final class Dashboard extends Component
{
    use InteractsWithPanelResource;

    public function render(): mixed
    {
        $navigation = app(ResourceRegistry::class)->navigation();

        return view('panel::livewire.dashboard', array_merge($this->sharedPanelData(), [
            'widgets' => app(WidgetRegistry::class)->all(),
            'resourceLinks' => NavigationBuilder::flatten($navigation),
        ]))->title(__('panel::panel.breadcrumbs.dashboard'));
    }
}
