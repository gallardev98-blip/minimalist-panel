<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelLayout
{
    public static function densidad(): string
    {
        $densidad = (string) config('panel.layout.density', 'comfortable');

        return in_array($densidad, ['comfortable', 'compact'], true) ? $densidad : 'comfortable';
    }

    public static function anchoContenido(): string
    {
        $ancho = (string) config('panel.layout.content_width', 'full');

        return in_array($ancho, ['full', 'boxed'], true) ? $ancho : 'full';
    }

    public static function sidebarColapsable(): bool
    {
        return (bool) config('panel.layout.sidebar_collapsible', false);
    }

    public static function anchoSidebarColapsado(): string
    {
        return (string) config('panel.layout.sidebar_collapsed_width', '4.5rem');
    }

    public static function mostrarVersion(): bool
    {
        return (bool) config('panel.layout.show_version', true);
    }

    public static function mostrarBreadcrumbs(): bool
    {
        return (bool) config('panel.layout.show_breadcrumbs', true);
    }

    public static function mostrarMenuMovil(): bool
    {
        return (bool) config('panel.layout.show_mobile_menu', true);
    }

    /** @return array<int, array{label: string, url?: string, route?: string, external?: bool}> */
    public static function enlacesFooter(): array
    {
        $enlaces = config('panel.layout.footer_links', []);

        return is_array($enlaces) ? $enlaces : [];
    }

    public static function marca(string $clave, mixed $defecto = null): mixed
    {
        return config('panel.brand.'.$clave, $defecto);
    }

    public static function authUi(string $clave, mixed $defecto = null): mixed
    {
        return config('panel.auth_ui.'.$clave, $defecto);
    }

    public static function cssPersonalizado(): ?string
    {
        $css = config('panel.customization.css');

        return is_string($css) && $css !== '' ? $css : null;
    }

    public static function vistaHead(): ?string
    {
        $vista = config('panel.customization.head_view');

        return is_string($vista) && $vista !== '' ? $vista : null;
    }

    public static function claseDensidad(): string
    {
        return self::densidad() === 'compact' ? 'panel-density-compact' : 'panel-density-comfortable';
    }

    public static function claseAnchoContenido(): string
    {
        return self::anchoContenido() === 'boxed' ? 'panel-content-boxed' : 'panel-content-full';
    }

    /** @return array<string, string> */
    public static function variablesCss(): array
    {
        $compacto = self::densidad() === 'compact';

        return array_merge([
            'panel-density-padding' => $compacto ? '1rem' : '1.5rem',
            'panel-density-gap' => $compacto ? '0.5rem' : '0.75rem',
            'panel-content-max-width' => self::anchoContenido() === 'boxed' ? '80rem' : 'none',
            'panel-sidebar-collapsed-width' => self::anchoSidebarColapsado(),
            'panel-brand-logo-height' => (string) self::marca('logo_height', '2rem'),
            'panel-header-height' => '4rem',
        ], PanelRendimiento::variablesCss());
    }

    public static function urlFondoAuth(): ?string
    {
        $fondo = self::authUi('background');

        if (! is_string($fondo) || $fondo === '') {
            return null;
        }

        if (str_starts_with($fondo, 'http://') || str_starts_with($fondo, 'https://') || str_starts_with($fondo, '//')) {
            return $fondo;
        }

        if (str_starts_with($fondo, 'linear-gradient') || str_starts_with($fondo, 'radial-gradient')) {
            return $fondo;
        }

        return asset($fondo);
    }

    public static function urlImagenAuth(): ?string
    {
        $imagen = self::authUi('image');

        if (! is_string($imagen) || $imagen === '') {
            return null;
        }

        return str_starts_with($imagen, 'http://') || str_starts_with($imagen, 'https://') || str_starts_with($imagen, '//')
            ? $imagen
            : asset($imagen);
    }

    public static function layoutAuthSplit(): bool
    {
        return self::authUi('layout', 'centered') === 'split' && self::urlImagenAuth() !== null;
    }

    public static function modo(): string
    {
        $modo = (string) config('panel.layout.mode', 'sidebar');

        return in_array($modo, ['sidebar', 'topbar', 'dual'], true) ? $modo : 'sidebar';
    }

    public static function posicionSidebar(): string
    {
        $pos = (string) config('panel.layout.sidebar_position', 'left');

        return in_array($pos, ['left', 'right'], true) ? $pos : 'left';
    }

    public static function tablaRayada(): bool
    {
        return (bool) config('panel.layout.table_striped', false);
    }

    public static function tablaCompacta(): bool
    {
        return (bool) config('panel.layout.table_compact', false);
    }

    public static function tablaCabeceraFija(): bool
    {
        return (bool) config('panel.layout.table_sticky_header', true);
    }

    public static function filasClicables(): bool
    {
        return (bool) config('panel.layout.index.clickable_rows', true);
    }

    public static function tarjetasMovil(): bool
    {
        return (bool) config('panel.layout.index.mobile_cards', true);
    }

    public static function columnasOcultables(): bool
    {
        return (bool) config('panel.layout.index.column_toggle', true);
    }

    public static function vistaRapida(): bool
    {
        return (bool) config('panel.layout.index.quick_view', true);
    }

    public static function presetsFiltros(): bool
    {
        return (bool) config('panel.layout.index.filter_presets', true);
    }

    public static function seleccionGlobalActiva(): bool
    {
        return (bool) config('panel.layout.index.select_all_matching', true);
    }

    public static function maximoSeleccionGlobal(): int
    {
        return max(1, (int) config('panel.bulk_select_all_max', 500));
    }

    public static function previewBulk(): bool
    {
        return (bool) config('panel.layout.index.bulk_preview', true);
    }

    public static function validacionInlineForm(): bool
    {
        return (bool) config('panel.forms.validate_inline', true);
    }

    public static function borradorFormulario(): bool
    {
        return (bool) config('panel.forms.draft_autosave', true);
    }

    public static function focoFormulario(): bool
    {
        return (bool) config('panel.forms.focus_on_open', true);
    }

    public static function importacionGuiada(): bool
    {
        return (bool) config('panel.import.guided_summary', true);
    }

    public static function busquedaGlobal(): bool
    {
        return (bool) config('panel.layout.global_search', true);
    }

    public static function atajoBusquedaGlobal(): bool
    {
        return (bool) config('panel.layout.global_search_shortcut', true);
    }

    public static function prefijoTitulo(): ?string
    {
        $prefijo = config('panel.layout.title_prefix');

        return is_string($prefijo) && $prefijo !== '' ? $prefijo : null;
    }

    public static function sufijoTitulo(): ?string
    {
        $sufijo = config('panel.layout.title_suffix');

        return is_string($sufijo) && $sufijo !== '' ? $sufijo : null;
    }

    /** @return list<int> */
    public static function opcionesPorPagina(): array
    {
        $opciones = config('panel.layout.per_page_options', [15, 25, 50, 100]);

        if (! is_array($opciones) || $opciones === []) {
            return [15, 25, 50, 100];
        }

        return array_values(array_map('intval', $opciones));
    }

    public static function claseModo(): string
    {
        return 'panel-shell--'.self::modo();
    }

    public static function clasePosicionSidebar(): string
    {
        return self::posicionSidebar() === 'right' ? 'panel-sidebar-right' : 'panel-sidebar-left';
    }

    public static function clasesTabla(): string
    {
        $clases = [];

        if (self::tablaRayada()) {
            $clases[] = 'panel-table-striped';
        }

        if (self::tablaCompacta()) {
            $clases[] = 'panel-table-compact';
        }

        if (self::tablaCabeceraFija()) {
            $clases[] = 'panel-table-sticky-header';
        }

        return implode(' ', $clases);
    }

    public static function modoFiltros(): string
    {
        $modo = (string) config('panel.layout.filters.mode', 'collapsible');

        return in_array($modo, ['inline', 'collapsible'], true) ? $modo : 'collapsible';
    }

    public static function filtrosAbiertosPorDefecto(): bool
    {
        return (bool) config('panel.layout.filters.default_open', false);
    }

    public static function recordarEstadoFiltros(): bool
    {
        return (bool) config('panel.layout.filters.remember_state', true);
    }

    public static function tituloPagina(?string $titulo = null): string
    {
        $base = $titulo ?? (string) config('panel.brand.name', 'Panel');
        $prefijo = self::prefijoTitulo();
        $sufijo = self::sufijoTitulo();

        return trim(($prefijo ? $prefijo.' ' : '').$base.($sufijo ? ' '.$sufijo : ''));
    }

    public static function usaSidebar(): bool
    {
        return in_array(self::modo(), ['sidebar', 'dual'], true);
    }

    public static function usaTopbar(): bool
    {
        return in_array(self::modo(), ['topbar', 'dual'], true);
    }

    public static function muestraMarcaTopbar(): bool
    {
        return self::modo() === 'topbar';
    }

    public static function claseBodyLayout(): string
    {
        return 'panel-layout-'.self::modo();
    }
}
