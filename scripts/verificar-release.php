<?php

declare(strict_types=1);

/**
 * Comprueba que Package::VERSION, CHANGELOG y composer.json estén alineados antes de etiquetar.
 * Uso: php scripts/verificar-release.php
 */

$raiz = dirname(__DIR__);
$versionEsperada = null;

require $raiz.'/vendor/autoload.php';

$versionEsperada = MyLaravelTools\Panel\Support\Package::VERSION;
$errores = [];

$changelog = file_get_contents($raiz.'/CHANGELOG.md') ?: '';
if (! preg_match('/## \['.preg_quote($versionEsperada, '/').'\]/', $changelog)) {
    $errores[] = "CHANGELOG.md no tiene entrada para {$versionEsperada}";
}

$composer = json_decode(file_get_contents($raiz.'/composer.json') ?: '{}', true);
if (! is_array($composer)) {
    $errores[] = 'composer.json inválido';
} elseif (($composer['name'] ?? '') !== 'mylaraveltools/panel') {
    $errores[] = 'composer.json name debe ser mylaraveltools/panel';
}

$readme = file_get_contents($raiz.'/README.md') ?: '';
if (! str_contains($readme, $versionEsperada) && ! str_contains($readme, 'v'.$versionEsperada)) {
    $errores[] = "README.md no menciona la versión {$versionEsperada} (roadmap o sección release)";
}

if ($errores !== []) {
    fwrite(STDERR, "Release {$versionEsperada} — fallos:\n- ".implode("\n- ", $errores)."\n");
    exit(1);
}

echo "OK — listo para etiquetar v{$versionEsperada}\n";
echo "  git tag -a v{$versionEsperada} -m \"v{$versionEsperada}\"\n";
echo "  git push origin main v{$versionEsperada}\n";
