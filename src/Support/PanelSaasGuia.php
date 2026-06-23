<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelSaasGuia
{
    public static function codigo(): string
    {
        return <<<'PHP'
# Kit SaaS en un comando
php artisan panel:install --saas
php artisan migrate

# Genera:
# - app/Models/Tenant.php + migración tenants
# - app/Panel/Resources/TenantResource.php (CustomField saas-plan)
# - resources/views/panel/saas/*.blade.php
# - config/panel.php → extensions.field_views, slots.main.before, widgets

# Multi-tenant real: añade tenant_id a tus modelos y scope global
# PanelExtensions::registrarVistaCampo() también válido en AppServiceProvider::boot
PHP;
    }

    /** @return list<string> */
    public static function pasos(): array
    {
        return [
            __('panel::panel.documentation.saas_step_install'),
            __('panel::panel.documentation.saas_step_extensions'),
            __('panel::panel.documentation.saas_step_tenant'),
        ];
    }
}
