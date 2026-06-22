<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Actions;

use Closure;
use Illuminate\Database\Eloquent\Model;
use MyLaravelTools\Panel\Support\PanelImpersonation;

final class RowAction
{
    private string $label;

    private ?string $icon = null;

    private ?string $confirmation = null;

    private string $color = 'default';

    private bool $isLink = false;

    /** @var Closure(Model, class-string): bool|null */
    private ?Closure $visible = null;

    /** @var Closure(Model, class-string): bool|null */
    private ?Closure $authorize = null;

    /** @var Closure(Model): void|null */
    private ?Closure $handler = null;

    /** @var Closure(Model, string): string|null */
    private ?Closure $urlResolver = null;

    private function __construct(
        private readonly string $name,
    ) {
        $this->label = str($name)->headline()->toString();
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public static function view(): self
    {
        return self::make('view')
            ->label(__('panel::panel.view'))
            ->icon('eye')
            ->link()
            ->authorize(fn (Model $record, string $resourceClass): bool => $resourceClass::authorize('view', $record));
    }

    public static function edit(): self
    {
        return self::make('edit')
            ->label(__('panel::panel.edit'))
            ->icon('pencil')
            ->link()
            ->color('primary')
            ->visible(fn (Model $record): bool => ! method_exists($record, 'trashed') || ! $record->trashed())
            ->authorize(fn (Model $record, string $resourceClass): bool => $resourceClass::authorize('update', $record));
    }

    public static function delete(): self
    {
        return self::make('delete')
            ->label(__('panel::panel.delete'))
            ->icon('trash-2')
            ->color('rose')
            ->confirm(__('panel::panel.confirm_delete'))
            ->visible(fn (Model $record): bool => ! method_exists($record, 'trashed') || ! $record->trashed())
            ->authorize(fn (Model $record, string $resourceClass): bool => $resourceClass::authorize('delete', $record));
    }

    public static function restore(): self
    {
        return self::make('restore')
            ->label(__('panel::panel.restore'))
            ->icon('rotate-ccw')
            ->color('emerald')
            ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed())
            ->authorize(fn (Model $record, string $resourceClass): bool => $resourceClass::authorize('restore', $record));
    }

    public static function forceDelete(): self
    {
        return self::make('forceDelete')
            ->label(__('panel::panel.delete'))
            ->icon('trash-2')
            ->color('rose')
            ->confirm(__('panel::panel.confirm_force_delete'))
            ->visible(fn (Model $record): bool => method_exists($record, 'trashed') && $record->trashed())
            ->authorize(fn (Model $record, string $resourceClass): bool => $resourceClass::authorize('forceDelete', $record));
    }

    public static function impersonate(): self
    {
        return self::make('impersonate')
            ->label(__('panel::panel.impersonate.action'))
            ->icon('user')
            ->color('primary')
            ->confirm(__('panel::panel.impersonate.confirm'))
            ->visible(fn (Model $record, string $resourceClass): bool => PanelImpersonation::canImpersonate($record))
            ->authorize(fn (Model $record, string $resourceClass): bool => PanelImpersonation::canImpersonate($record));
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function icon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function confirm(?string $message = null): self
    {
        $this->confirmation = $message ?? __('panel::panel.confirm_bulk_action');

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function link(bool $isLink = true): self
    {
        $this->isLink = $isLink;

        return $this;
    }

    /** @param Closure(Model, class-string): bool $callback */
    public function visible(Closure $callback): self
    {
        $this->visible = $callback;

        return $this;
    }

    /** @param Closure(Model, class-string): bool $callback */
    public function authorize(Closure $callback): self
    {
        $this->authorize = $callback;

        return $this;
    }

    /** @param Closure(Model): void $handler */
    public function handle(Closure $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /** @param Closure(Model, string): string $resolver */
    public function url(Closure $resolver): self
    {
        $this->urlResolver = $resolver;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function isLink(): bool
    {
        return $this->isLink;
    }

    public function isVisible(Model $record, string $resourceClass): bool
    {
        if ($this->visible !== null && ! ($this->visible)($record, $resourceClass)) {
            return false;
        }

        if ($this->authorize !== null && ! ($this->authorize)($record, $resourceClass)) {
            return false;
        }

        return true;
    }

    public function resolveUrl(Model $record, string $resourceSlug): ?string
    {
        if ($this->urlResolver !== null) {
            return ($this->urlResolver)($record, $resourceSlug);
        }

        return match ($this->name) {
            'view' => route('panel.resources.show', [
                'resource' => $resourceSlug,
                'record' => $record->getKey(),
            ]),
            'edit' => route('panel.resources.edit', [
                'resource' => $resourceSlug,
                'record' => $record->getKey(),
            ]),
            default => null,
        };
    }

    public function run(Model $record): void
    {
        if ($this->handler === null) {
            return;
        }

        ($this->handler)($record);
    }
}
