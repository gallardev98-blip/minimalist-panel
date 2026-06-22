<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

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
