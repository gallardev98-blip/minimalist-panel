<?php



declare(strict_types=1);



namespace MyLaravelTools\Panel\Support;



final class PanelPlayground

{

    private const CLAVE_SESION = 'panel.playground';



    public static function aplicarDesdeSesion(): void
    {
        $sobreescrituras = session(self::CLAVE_SESION, []);

        if (! is_array($sobreescrituras) || $sobreescrituras === []) {
            return;
        }

        foreach ($sobreescrituras as $clave => $valor) {
            if (! is_string($clave) || $clave === '') {
                continue;
            }

            config()->set('panel.'.$clave, $valor);
        }

        if (isset($sobreescrituras['theme.preset']) && is_string($sobreescrituras['theme.preset'])) {
            self::reconstruirTemaDesdePreset($sobreescrituras);
        }
    }

    /** @param array<string, mixed> $sobreescrituras */
    private static function reconstruirTemaDesdePreset(array $sobreescrituras): void
    {
        $preset = $sobreescrituras['theme.preset'];
        $base = ThemePresets::definiciones()[$preset] ?? [];

        if ($base === []) {
            return;
        }

        $tema = $base;

        foreach ($sobreescrituras as $clave => $valor) {
            if (! str_starts_with($clave, 'theme.') || $clave === 'theme.preset') {
                continue;
            }

            data_set($tema, substr($clave, strlen('theme.')), $valor);
        }

        $tema['preset'] = $preset;
        config(['panel.theme' => $tema]);
    }



    /** @return array<string, mixed> */

    public static function valores(): array

    {

        $anidado = [];



        foreach (PanelDocumentacion::clavesInteractivas() as $clave) {

            data_set($anidado, $clave, self::leer($clave));

        }



        return $anidado;

    }



    /** @return array<string, mixed> */

    public static function sobreescrituras(): array

    {

        $datos = session(self::CLAVE_SESION, []);



        return is_array($datos) ? $datos : [];

    }



    /** @return array<string, mixed> */

    public static function exportarArrayAnidado(): array

    {

        $anidado = [];



        foreach (self::sobreescrituras() as $clave => $valor) {

            if (! is_string($clave) || $clave === '') {

                continue;

            }



            data_set($anidado, $clave, $valor);

        }



        return $anidado;

    }



    public static function exportarFragmento(): string

    {

        $anidado = self::exportarArrayAnidado();

        if ($anidado === []) {

            return '';

        }



        $lineas = [];



        foreach ($anidado as $clave => $valor) {

            $clavePhp = "'".addcslashes((string) $clave, "'\\")."'";

            $lineas[] = is_array($valor)

                ? $clavePhp.' => '.self::formatearPhp($valor, 0).','

                : $clavePhp.' => '.self::formatearValorPhp($valor).',';

        }



        return implode("\n", $lineas);

    }



    public static function exportarArchivo(): string

    {

        $fragmento = self::exportarFragmento();

        if ($fragmento === '') {

            return '';

        }



        return "<?php\n\nreturn [\n".$fragmento."\n];\n";

    }



    public static function exportarEntrada(string $clave): string

    {

        $valor = self::sobreescrituras()[$clave] ?? self::leer($clave);

        $anidado = [];

        data_set($anidado, $clave, $valor);



        return self::formatearEntradaAnidada($anidado);

    }



    /** @return list<array{clave: string, etiqueta: string, fragmento: string}> */

    public static function listarCambios(): array

    {

        $lista = [];



        foreach (self::sobreescrituras() as $clave => $valor) {

            if (! is_string($clave) || $clave === '') {

                continue;

            }



            $anidado = [];

            data_set($anidado, $clave, $valor);



            $lista[] = [

                'clave' => $clave,

                'etiqueta' => PanelDocumentacion::etiquetaPorClave($clave) ?? $clave,

                'fragmento' => self::formatearEntradaAnidada($anidado),

            ];

        }



        return $lista;

    }



    public static function guardar(string $clave, mixed $valor): void

    {

        $sobreescrituras = session(self::CLAVE_SESION, []);

        $sobreescrituras = is_array($sobreescrituras) ? $sobreescrituras : [];

        $sobreescrituras[$clave] = $valor;

        session([self::CLAVE_SESION => $sobreescrituras]);

    }



    public static function reiniciar(): void

    {

        session()->forget(self::CLAVE_SESION);

    }



    public static function tieneSobreescrituras(): bool

    {

        return self::sobreescrituras() !== [];

    }



    public static function leer(string $clave): mixed

    {

        $sobreescrituras = session(self::CLAVE_SESION, []);



        if (is_array($sobreescrituras) && array_key_exists($clave, $sobreescrituras)) {

            return $sobreescrituras[$clave];

        }



        return config('panel.'.$clave);

    }



    /** @param array<string, mixed> $datos */

    private static function formatearEntradaAnidada(array $datos): string

    {

        $lineas = [];



        foreach ($datos as $clave => $valor) {

            $clavePhp = "'".addcslashes((string) $clave, "'\\")."'";

            $lineas[] = is_array($valor)

                ? $clavePhp.' => '.self::formatearPhp($valor, 0).','

                : $clavePhp.' => '.self::formatearValorPhp($valor).',';

        }



        return implode("\n", $lineas);

    }



    /** @param array<int|string, mixed> $datos */

    private static function formatearPhp(array $datos, int $nivel): string

    {

        $indent = str_repeat('    ', $nivel);

        $indentHijo = str_repeat('    ', $nivel + 1);

        $lineas = ['['];



        foreach ($datos as $clave => $valor) {

            $clavePhp = is_int($clave) ? (string) $clave : "'".addcslashes((string) $clave, "'\\")."'";

            $lineas[] = is_array($valor)

                ? $indentHijo.$clavePhp.' => '.self::formatearPhp($valor, $nivel + 1).','

                : $indentHijo.$clavePhp.' => '.self::formatearValorPhp($valor).',';

        }



        $lineas[] = $indent.']';



        return implode("\n", $lineas);

    }



    private static function formatearValorPhp(mixed $valor): string

    {

        if (is_bool($valor)) {

            return $valor ? 'true' : 'false';

        }



        if (is_int($valor) || is_float($valor)) {

            return (string) $valor;

        }



        if ($valor === null) {

            return 'null';

        }



        if (is_array($valor)) {

            return self::formatearPhp($valor, 0);

        }



        return "'".addcslashes((string) $valor, "'\\")."'";

    }

}


