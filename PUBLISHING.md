# Publicar `mylaraveltools/panel` en Packagist

Instalación pública: `composer require mylaraveltools/panel`.

| Recurso | URL |
|---------|-----|
| Packagist | https://packagist.org/packages/mylaraveltools/panel |
| Repositorio | https://github.com/gallardev98-blip/minimalist-panel |
| Demo local | `panel-demo/` en el monorepo (ver `panel-demo/DEPLOY.md` para online) |

> **Vendor:** `mylaraveltools/panel` (ecosistema **My Laravel Tools**, como `mylaraveltools/alertas`).  
> **Namespace PHP:** `MyLaravelTools\Panel` (sin cambios respecto a `mylaraveltools/minimalist`).

---

## Checklist rápido (cada release)

```bash
cd minimalist-panel-library
composer test
php scripts/verificar-release.php
```

- [ ] `Package::VERSION` = tag (p. ej. `0.36.0`)
- [ ] Entrada en `CHANGELOG.md` para esa versión
- [ ] `README.md` / `AGENTS.md` si cambia alcance
- [ ] Tests en verde (CI monorepo: `.github/workflows/panel-tests.yml`)
- [ ] Tag anotado y push a GitHub
- [ ] Packagist actualizado (webhook o **Update** manual)
- [ ] Probar en proyecto limpio: `composer require mylaraveltools/panel:^0.36`

---

## 1. Etiquetar release

Versión actual del paquete: ver `src/Support/Package.php` → `VERSION`.

```bash
cd minimalist-panel-library
composer test
php scripts/verificar-release.php

git add -A
git commit -m "chore: release v0.36.0"
git tag -a v0.36.0 -m "v0.36.0 — playground RelationManager + multi-panel"
git push origin main
git push origin v0.36.0
```

Convención: tag **`v` + semver** (`v0.36.0`). Packagist lee tags de Git, no el campo `version` en `composer.json`.

---

## 2. Registrar / actualizar Packagist

### Primera vez

1. Cuenta en [packagist.org](https://packagist.org)
2. **Submit** → `https://github.com/gallardev98-blip/minimalist-panel`
3. Confirma nombre `mylaraveltools/panel`

### Migración desde `mylaraveltools/minimalist`

`composer.json` incluye `"replace": { "mylaraveltools/minimalist": "0.24.0" }`.  
Marca el paquete antiguo como **abandoned** con sucesor `mylaraveltools/panel`.

### Auto-update (recomendado)

Packagist → paquete → **Settings** → **GitHub Service Hook** activado.

---

## 3. Instalar en Laravel (producción)

```bash
composer require mylaraveltools/panel:^0.36
php artisan panel:install
php artisan migrate
npm install && npm run build
```

Opcional: `mylaraveltools/alertas`, `spatie/laravel-permission`.

Tras `composer update`:

```bash
php artisan panel:upgrade-config --dry-run
php artisan panel:upgrade-config
php artisan panel:doctor
```

---

## 4. Historial de versiones (resumen)

| Rango | Hitos |
|-------|--------|
| v0.21–v0.24 | Rename a `panel`, suplantación, layout topbar/dual, slots |
| v0.25–v0.27 | Playground, `panel:doctor`, starter/scaffold |
| v0.28–v0.29 | Multi-panel, `panel_route()`, `install --multi` |
| v0.30–v0.31 | Playground import/permisos, guía extensiones, smoke CI |
| v0.32–v0.33 | `install --saas`, `panel:upgrade-config` |
| v0.34–v0.36 | Doctor config, panel-demo unificado, RelationManager/multi en playground |

Detalle completo: [CHANGELOG.md](CHANGELOG.md).

---

## 5. Demo online (`panel-demo`)

El host de prueba vive en `../panel-demo` (monorepo local). Para publicarlo:

1. Sube **monorepo** o carpeta `panel-demo` a un repo Git
2. En producción usa **Packagist**, no path repository (ver [panel-demo/DEPLOY.md](../panel-demo/DEPLOY.md))
3. Despliegue listo: `panel-demo/render.yaml` (Render.com)

Playground público: `GET /playground` (`documentation.enabled` en config).

---

## 6. Solución de problemas

| Problema | Acción |
|----------|--------|
| Packagist no ve el tag | `git push origin vX.Y.Z`; Update manual en Packagist |
| `composer require` versión antigua | `composer clear-cache`; comprueba tag en GitHub |
| Clase no encontrada tras update | `php artisan panel:upgrade-config` + `composer dump-autoload` |
| Vistas desactualizadas | `php artisan panel:upgrade-views --force` |
