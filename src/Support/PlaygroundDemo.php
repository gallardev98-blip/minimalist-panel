<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

use MyLaravelTools\Panel\Widgets\ViewWidget;

final class PlaygroundDemo
{
    /** @return array<int, array<string, mixed>> */
    public static function navegacion(): array
    {
        return [
            [
                'type' => 'group',
                'label' => 'Catálogo',
                'icon' => 'folder',
                'open' => true,
                'children' => [
                    self::enlace('Productos', 'package', 'products'),
                    self::enlace('Categorías', 'layers', 'categories'),
                ],
            ],
            [
                'type' => 'group',
                'label' => 'Ventas',
                'icon' => 'shopping-cart',
                'open' => false,
                'children' => [
                    self::enlace('Pedidos', 'file', 'orders'),
                    self::enlace('Clientes', 'users', 'customers'),
                ],
            ],
            [
                'type' => 'link',
                'label' => 'Informes',
                'icon' => 'bar-chart',
                'url' => '#informes',
                'slug' => 'reports',
            ],
        ];
    }

    /** @return array{name: string, email: string, initial: string} */
    public static function usuario(): array
    {
        return [
            'name' => 'Admin Demo',
            'email' => 'demo@panel.test',
            'initial' => 'A',
        ];
    }

    public static function widgetVista(): ViewWidget
    {
        return ViewWidget::make(
            __('panel::panel.documentation.view_widget_label'),
            'panel::widgets.playground-timeline',
            fn (): array => [
                'items' => [
                    ['label' => __('panel::panel.documentation.view_widget_item_a'), 'value' => '12'],
                    ['label' => __('panel::panel.documentation.view_widget_item_b'), 'value' => '5'],
                    ['label' => __('panel::panel.documentation.view_widget_item_c'), 'value' => '98%'],
                ],
            ],
        )->columnSpan(2);
    }

    public static function etiquetaModo(string $modo): string
    {
        return match ($modo) {
            'topbar' => __('panel::panel.documentation.layout_mode_topbar'),
            'dual' => __('panel::panel.documentation.layout_mode_dual'),
            default => __('panel::panel.documentation.layout_mode_sidebar'),
        };
    }

    /** @return list<array{valid: bool, cells: list<string>, error?: string}> */
    public static function filasImportPreview(): array
    {
        return [
            ['valid' => true, 'cells' => ['Widget A', '12,50', __('panel::panel.documentation.active')]],
            ['valid' => false, 'cells' => ['', 'abc', __('panel::panel.documentation.active')], 'error' => __('panel::panel.documentation.import_preview_error')],
            ['valid' => true, 'cells' => ['Widget B', '8,00', __('panel::panel.documentation.draft')]],
        ];
    }

    /** @return list<array{label: string, visible: bool, permiso?: string}> */
    public static function menuPermisos(): array
    {
        $activo = (bool) config('panel.permissions.enabled', false);
        $gestionar = (string) config('panel.permissions.manage_permission', 'manage users');

        return [
            ['label' => __('panel::panel.breadcrumbs.dashboard'), 'visible' => true],
            ['label' => __('panel::panel.documentation.perm_preview_users'), 'visible' => ! $activo, 'permiso' => $gestionar],
            ['label' => __('panel::panel.documentation.perm_preview_roles'), 'visible' => ! $activo, 'permiso' => 'manage roles'],
            ['label' => __('panel::panel.documentation.sample_row').' 1', 'visible' => true],
        ];
    }

    /** @return list<array{id: string, titulo: string, tipo: string, filas: list<array{cells: list<string>}>}> */
    public static function pestanasRelaciones(): array
    {
        return [
            [
                'id' => 'hasOne',
                'titulo' => __('panel::panel.documentation.rel_preview_tab_detail'),
                'tipo' => 'hasOne',
                'filas' => [['cells' => ['24 meses', __('panel::panel.documentation.rel_preview_origin')]]],
            ],
            [
                'id' => 'morphMany',
                'titulo' => __('panel::panel.documentation.rel_preview_tab_reviews'),
                'tipo' => 'morphMany',
                'filas' => [
                    ['cells' => ['Ana G.', '★★★★★']],
                    ['cells' => ['Luis M.', '★★★★☆']],
                ],
            ],
            [
                'id' => 'belongsToMany',
                'titulo' => __('panel::panel.documentation.rel_preview_tab_tags'),
                'tipo' => 'belongsToMany',
                'filas' => [
                    ['cells' => [__('panel::panel.documentation.rel_preview_tag_new')]],
                    ['cells' => [__('panel::panel.documentation.rel_preview_tag_offer')]],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private static function enlace(string $etiqueta, string $icono, string $slug): array
    {
        return [
            'type' => 'link',
            'label' => $etiqueta,
            'icon' => $icono,
            'url' => '#'.$slug,
            'slug' => $slug,
        ];
    }
}
