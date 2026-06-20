<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Widgets;

use Closure;

final class ViewWidget
{
    private string $label;

    private string $view;

    /** @var Closure(): array<string, mixed> */
    private Closure $dataResolver;

    private ?string $url = null;

    private int $columnSpan = 1;

    /** @param Closure(): array<string, mixed>|array<string, mixed> $data */
    private function __construct(string $label, string $view, Closure|array $data)
    {
        $this->label = $label;
        $this->view = $view;
        $this->dataResolver = is_array($data) ? fn (): array => $data : $data;
    }

    /** @param Closure(): array<string, mixed>|array<string, mixed> $data */
    public static function make(string $label, string $view, Closure|array $data = []): self
    {
        return new self($label, $view, $data);
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function columnSpan(int $span): self
    {
        $this->columnSpan = max(1, min(4, $span));

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getView(): string
    {
        return $this->view;
    }

    /** @return array<string, mixed> */
    public function getViewData(): array
    {
        return ($this->dataResolver)();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getColumnSpan(): int
    {
        return $this->columnSpan;
    }

    public function getIcon(): ?string
    {
        return null;
    }

    public function getColor(): string
    {
        return 'indigo';
    }

    public function getValue(): string
    {
        return '';
    }
}
