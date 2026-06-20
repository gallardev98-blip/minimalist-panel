<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;

final class RichTextField extends Field
{
    public function getType(): string
    {
        return 'rich-text';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        $value = parent::hydrateForForm($record);

        return is_string($value) ? $value : '';
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if (! is_string($value)) {
            return ['value' => null, 'include' => true];
        }

        $sanitized = strip_tags($value, '<p><br><strong><em><u><ul><ol><li><a><h2><h3><blockquote>');

        return [
            'value' => $sanitized === '' ? null : $sanitized,
            'include' => true,
        ];
    }

    /** @return array<int, string> */
    public function getRules(?Model $record = null): array
    {
        return array_merge(parent::getRules($record), ['nullable', 'string', 'max:65535']);
    }

    protected function meta(): array
    {
        return [
            'toolbar' => ['bold', 'italic', 'underline', 'list', 'link'],
        ];
    }
}
