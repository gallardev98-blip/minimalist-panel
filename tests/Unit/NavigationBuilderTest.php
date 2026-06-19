<?php

declare(strict_types=1);

namespace Panel\Minimalist\Tests\Unit;

use Panel\Minimalist\Support\NavigationBuilder;
use Panel\Minimalist\Tests\TestCase;

final class NavigationBuilderTest extends TestCase
{
    public function test_flatten_expands_groups_into_links(): void
    {
        $navigation = [
            [
                'type' => 'group',
                'label' => 'Catálogo',
                'icon' => 'package',
                'children' => [
                    [
                        'type' => 'link',
                        'label' => 'Productos',
                        'url' => '/admin/resources/products',
                        'icon' => 'package',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Marcas',
                        'url' => '/admin/resources/brands',
                        'icon' => 'bookmark',
                    ],
                ],
            ],
            [
                'type' => 'link',
                'label' => 'Usuarios',
                'url' => '/admin/resources/users',
                'icon' => 'users',
            ],
        ];

        $flat = NavigationBuilder::flatten($navigation);

        $this->assertCount(3, $flat);
        $this->assertSame('Productos', $flat[0]['label']);
        $this->assertSame('/admin/resources/products', $flat[0]['url']);
        $this->assertSame('Usuarios', $flat[2]['label']);
    }

    public function test_groups_expand_when_configured(): void
    {
        config(['panel.navigation_groups_expanded' => true]);

        $navigation = NavigationBuilder::build([
            [
                'type' => 'group',
                'label' => 'Ventas',
                'children' => [
                    ['label' => 'Pedidos', 'url' => '/admin/resources/orders', 'icon' => 'shopping-cart'],
                ],
            ],
        ], app(\Panel\Minimalist\Support\ResourceRegistry::class));

        $this->assertTrue($navigation[0]['open']);
    }

    public function test_custom_links_are_not_current_on_dashboard(): void
    {
        $this->get('/admin');

        $isCurrent = NavigationBuilder::linkIsCurrent([
            'label' => 'Resumen de ventas',
            'url' => url('/admin'),
            'icon' => 'bar-chart',
        ]);

        $this->assertFalse($isCurrent);
    }
}
