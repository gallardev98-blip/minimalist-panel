<?php

declare(strict_types=1);

namespace Panel\Minimalist\Columns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

final class ImageColumn extends Column
{
    protected string $disk = 'public';

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function getType(): string
    {
        return 'image';
    }

    public function resolve(Model $record): mixed
    {
        $path = parent::resolve($record);

        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk($this->disk)->url((string) $path);
    }
}
