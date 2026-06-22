<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Fields;

use Illuminate\Database\Eloquent\Model;

final class RepeaterField extends Field
{
    /** @var array<string, string> */
    protected array $columnas = ['value' => 'Valor'];

    protected int $minFilas = 0;

    protected int $maxFilas = 20;

    /** @param array<string, string> $columnas */
    public function columns(array $columnas): static
    {
        $this->columnas = $columnas !== [] ? $columnas : $this->columnas;

        return $this;
    }

    public function minRows(int $min): static
    {
        $this->minFilas = max(0, $min);

        return $this;
    }

    public function maxRows(int $max): static
    {
        $this->maxFilas = max(1, $max);

        return $this;
    }

    public function getType(): string
    {
        return 'repeater';
    }

    /** @return array<int, string|\Illuminate\Contracts\Validation\ValidationRule> */
    public function getRules(?Model $record = null): array
    {
        $reglas = parent::getRules($record);
        $reglas[] = 'array';
        $reglas[] = 'max:'.$this->maxFilas;

        if ($this->minFilas > 0) {
            $reglas[] = 'min:'.$this->minFilas;
        }

        return $reglas;
    }

    public function hydrateForForm(?Model $record): mixed
    {
        $valor = parent::hydrateForForm($record);

        if (is_string($valor) && $valor !== '') {
            $decodificado = json_decode($valor, true);

            return is_array($decodificado) ? self::normalizarFilas($decodificado) : [];
        }

        return is_array($valor) ? self::normalizarFilas($valor) : [];
    }

    /**
     * @return array{value: mixed, include: bool}
     */
    public function dehydrateForStorage(mixed $value, ?Model $record): array
    {
        if (! is_array($value) || $value === []) {
            return ['value' => null, 'include' => true];
        }

        $filas = self::normalizarFilas($value);

        return [
            'value' => $filas === [] ? null : json_encode($filas, JSON_UNESCAPED_UNICODE),
            'include' => true,
        ];
    }

    /** @return array<string, mixed> */
    protected function meta(): array
    {
        return [
            'columns' => $this->columnas,
            'min' => $this->minFilas,
            'max' => $this->maxFilas,
        ];
    }

    /**
     * @param array<int|string, mixed> $filas
     * @return list<array<string, string>>
     */
    private static function normalizarFilas(array $filas): array
    {
        $salida = [];

        foreach ($filas as $fila) {
            if (! is_array($fila)) {
                continue;
            }

            $limpia = [];

            foreach ($fila as $clave => $texto) {
                $clave = trim((string) $clave);

                if ($clave === '') {
                    continue;
                }

                $limpia[$clave] = is_scalar($texto) ? (string) $texto : '';
            }

            if ($limpia !== []) {
                $salida[] = $limpia;
            }
        }

        return $salida;
    }
}
