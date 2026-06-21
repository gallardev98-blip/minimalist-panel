# Publicar `mylaraveltools/minimalist` en Packagist

Guía para instalar con `composer require mylaraveltools/minimalist` sin path repository.

> **Vendor Packagist:** `mylaraveltools/minimalist` (marca **My Laravel Tools**, alineada con `mylaraveltools/alertas`). El namespace PHP sigue siendo `MyLaravelTools\Panel`.

## Requisitos previos

- Repositorio Git **público** en GitHub/GitLab
- Cuenta en [packagist.org](https://packagist.org)
- Tests en verde: `composer test`

## 1. Repositorio y tags

```bash
cd minimalist-panel-library
composer test

git init
git add .
git commit -m "chore: release v0.10.0"

git tag -a v0.9.0 -m "v0.9.0 — custom pages and Spatie permissions"
git tag -a v0.10.0 -m "v0.10.0 — integrated auth (login/register)"

git branch -M main
git remote add origin https://github.com/gallardev98-blip/minimalist-panel.git
git push -u origin main
git push origin v0.9.0 v0.10.0
```

> **Nota:** Si es el primer commit del repo, `v0.9.0` y `v0.10.0` apuntan al mismo código. Packagist indexará ambas versiones; usa `^0.10` para auth integrada.

## 2. Registrar en Packagist

1. Inicia sesión en [packagist.org](https://packagist.org)
2. **Submit** → URL del repo: `https://github.com/gallardev98-blip/minimalist-panel`
3. Packagist detecta `mylaraveltools/minimalist` desde `composer.json`

## 3. Auto-update (recomendado)

Packagist → tu paquete → **Settings** → activa **GitHub Service Hook**.

## 4. Instalar en un proyecto Laravel

```bash
composer require mylaraveltools/minimalist:^0.10
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

Última release: **`v0.20.0`**. Tras cambios, etiqueta y push:

```bash
composer test
git tag -a v0.20.0 -m "v0.20.0 — auth UX"
git push origin main v0.20.0
```

## Checklist

- [ ] `composer.json` — name, license, authors
- [ ] `README.md` y `CHANGELOG.md` actualizados
- [ ] `Package::VERSION` coincide con el tag
- [ ] `composer test` pasa
- [ ] Tag pusheado a GitHub
- [ ] Paquete actualizado en Packagist (hook o Update)
- [ ] `panel-demo` probado con `composer require mylaraveltools/minimalist:^0.20`
