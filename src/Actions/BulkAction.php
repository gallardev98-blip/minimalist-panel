<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Actions;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

final class BulkAction
{
    private string $label;

    private ?string $confirmation = null;

    private string $color = 'primary';

    /** @var Closure(Collection<int, Model>): void|null */
    private ?Closure $handler = null;

    private function __construct(
        private readonly string $name,
    ) {
        $this->label = str($name)->headline()->toString();
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function label(string $label): self
    {
        $this->label = $label;

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

    /** @param Closure(Collection<int, Model>): void $handler */
    public function handle(Closure $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

  public static function delete(?string $confirmation = null): self
    {
        return self::make('delete')
            ->label(__('panel::panel.bulk_delete'))
            ->color('rose')
            ->confirm($confirmation ?? __('panel::panel.confirm_bulk_delete'))
            ->handle(function (Collection $records): void {
                $records->each->delete();
            });
    }

    public static function restore(): self
    {
        return self::make('restore')
            ->label(__('panel::panel.bulk_restore'))
            ->color('emerald')
            ->confirm(__('panel::panel.confirm_bulk_restore'))
            ->handle(function (Collection $records): void {
                $records->each->restore();
            });
    }

    public static function forceDelete(): self
    {
        return self::make('forceDelete')
            ->label(__('panel::panel.bulk_force_delete'))
            ->color('rose')
            ->confirm(__('panel::panel.confirm_bulk_force_delete'))
            ->handle(function (Collection $records): void {
                $records->each->forceDelete();
            });
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    /** @param Collection<int, Model> $records */
    public function run(Collection $records): void
    {
        if ($this->handler === null) {
            return;
        }

        ($this->handler)($records);
    }
}
