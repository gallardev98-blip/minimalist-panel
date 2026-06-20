<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

final class FileField extends Field
{
    protected string $disk = 'public';

    protected string $directory = 'panel/files';

    /** @var array<int, string> */
    protected array $acceptedMimes = [];

    public function disk(string $disk): static
    {
        $this->disk = $disk;

        return $this;
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;

        return $this;
    }

    /** @param array<int, string> $mimes */
    public function acceptedMimes(array $mimes): static
    {
        $this->acceptedMimes = $mimes;

        return $this;
    }

    public function getType(): string
    {
        return 'file';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        if ($record === null) {
            return null;
        }

        return data_get($record, $this->name);
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if ($value instanceof TemporaryUploadedFile || $value instanceof UploadedFile) {
            if ($record !== null) {
                $this->deleteStoredFile((string) data_get($record, $this->name));
            }

            return [
                'value' => $value->store($this->directory, $this->disk),
                'include' => true,
            ];
        }

        if ($value === null || $value === '') {
            return ['value' => null, 'include' => false];
        }

        return [
            'value' => $value,
            'include' => true,
        ];
    }

    /** @return array<int, string> */
    public function getRules(?Model $record = null): array
    {
        $rules = parent::getRules($record);

        if ($record?->exists && ! $this->required) {
            $rules = array_values(array_filter($rules, fn (mixed $rule): bool => $rule !== 'required'));
            $rules[] = 'nullable';
        }

        $fileRules = ['file', 'max:5120'];

        if ($this->acceptedMimes !== []) {
            $fileRules[] = 'mimes:' . implode(',', $this->acceptedMimes);
        }

        return array_merge($rules, $fileRules);
    }

    public function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk($this->disk)->url($path);
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk($this->disk)->delete($path);
    }

    protected function meta(): array
    {
        return [
            'disk' => $this->disk,
            'directory' => $this->directory,
            'acceptedMimes' => $this->acceptedMimes,
        ];
    }
}
