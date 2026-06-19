<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire;

use Panel\Minimalist\Livewire\Concerns\InteractsWithPanelResource;
use Panel\Minimalist\Support\NavigationBuilder;
use Panel\Minimalist\Support\ResourceRegistry;
use Panel\Minimalist\Support\WidgetRegistry;
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
