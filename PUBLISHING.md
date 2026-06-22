# Publicar `mylaraveltools/panel` en Packagist

Guía para instalar con `composer require mylaraveltools/panel` sin path repository.

> **Vendor Packagist:** `mylaraveltools/panel` (marca **My Laravel Tools**, alineada con `mylaraveltools/alertas`). El namespace PHP sigue siendo `MyLaravelTools\Panel`.

> **Migración:** el paquete anterior `mylaraveltools/minimalist` queda reemplazado por `mylaraveltools/panel` vía `composer replace`. Registra el nuevo nombre en Packagist (mismo repo Git).

## Requisitos previos

- Repositorio Git **público** en GitHub/GitLab
- Cuenta en [packagist.org](https://packagist.org)
- Tests en verde: `composer test`

## 1. Repositorio y tags

```bash
cd minimalist-panel-library
composer test

git tag -a v0.21.0 -m "v0.21.0 — rename to mylaraveltools/panel, impersonation"
git push origin main v0.21.0
```

Repo: `https://github.com/gallardev98-blip/minimalist-panel`

## 2. Registrar en Packagist

1. Inicia sesión en [packagist.org](https://packagist.org)
2. **Submit** → URL del repo: `https://github.com/gallardev98-blip/minimalist-panel`
3. Packagist detecta `mylaraveltools/panel` desde `composer.json`

Si ya tenías `mylaraveltools/minimalist`, añade el nuevo paquete o marca el antiguo como abandonado con `mylaraveltools/panel` como sucesor.

## 3. Auto-update (recomendado)

Packagist → tu paquete → **Settings** → activa **GitHub Service Hook**.

## 4. Instalar en un proyecto Laravel

```bash
composer require mylaraveltools/panel:^0.21
php artisan panel:install
```

## 5. Versionado

| Tag | Contenido principal |
|-----|---------------------|
| `v0.9.0` | Pages custom, `PanelPermission`, Spatie opcional |
| `v0.10.0` | Auth integrada (`/admin/login`, `/register`) |
| `v0.17.0` | Import, locale, ChartWidget, email verify |
| `v0.18.0` | ViewWidget, progression, themeColors |
| `v0.19.0` | Import con vista previa |
| `v0.20.0` | Auth UX — redirect post-login, botón con puntos animados |
| `v0.21.0` | Rename `mylaraveltools/panel`, suplantación de usuario |

Última release: **`v0.21.0`**. Tras cambios, etiqueta y push:

```bash
composer test
git tag -a v0.21.0 -m "v0.21.0 — panel rename + impersonation"
git push origin main v0.21.0
```

## Checklist

- [ ] `composer.json` — `name: mylaraveltools/panel`, `replace` del nombre antiguo
- [ ] `README.md` y `CHANGELOG.md` actualizados
- [ ] `Package::VERSION` coincide con el tag
- [ ] `composer test` pasa
- [ ] Tag pusheado a GitHub
- [ ] Paquete actualizado en Packagist (hook o Update)
- [ ] `panel-demo` probado con `composer require mylaraveltools/panel:^0.21`
