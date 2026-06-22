<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;

final class KeyValueField extends Field
{
    public function getType(): string
    {
        return 'key-value';
    }

    public function hydrateForForm(?Model $record): mixed
    {
        $valor = parent::hydrateForForm($record);

        if (is_string($valor) && $valor !== '') {
            $decodificado = json_decode($valor, true);

            return is_array($decodificado) ? $decodificado : [];
        }

        return is_array($valor) ? $valor : [];
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if (! is_array($value) || $value === []) {
            return ['value' => null, 'include' => true];
        }

        $pares = [];

        foreach ($value as $clave => $texto) {
            $clave = trim((string) $clave);

            if ($clave === '') {
                continue;
            }

            $pares[$clave] = is_scalar($texto) ? (string) $texto : '';
        }

        return [
            'value' => $pares === [] ? null : json_encode($pares, JSON_UNESCAPED_UNICODE),
            'include' => true,
        ];
    }
}
