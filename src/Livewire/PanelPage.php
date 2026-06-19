<?php

declare(strict_types=1);

namespace Panel\Minimalist\Livewire;

use Panel\Minimalist\Livewire\Concerns\InteractsWithPanelResource;
use Panel\Minimalist\Pages\Page;
use Panel\Minimalist\Support\PageRegistry;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Layout('panel::layouts.app')]
final class PanelPage extends Component
{
    use InteractsWithPanelResource;

    public string $page = '';

    public function mount(string $page): void
    {
        $this->resolvePage($page);
        $this->page = $page;
    }

    public function render(): mixed
    {
        /** @var class-string<Page> $pageClass */
        $pageClass = $this->resolvePage($this->page);

        return view($pageClass::view(), array_merge(
            $this->sharedPanelData(),
            $pageClass::data(),
            ['pageClass' => $pageClass],
        ))->title($pageClass::label());
    }

    /** @return class-string<Page> */
    private function resolvePage(string $slug): string
    {
        $pageClass = app(PageRegistry::class)->findBySlug($slug);

        if ($pageClass === null) {
            throw new NotFoundHttpException("Page [{$slug}] not found.");
        }

        abort_unless($pageClass::canAccess(), 403);

        return $pageClass;
    }
}
