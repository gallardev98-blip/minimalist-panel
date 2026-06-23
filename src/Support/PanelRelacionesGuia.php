<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelRelacionesGuia
{
    /** @return list<array{id: string, titulo: string, descripcion: string, codigo: string}> */
    public static function pasos(): array
    {
        return [
            [
                'id' => 'hasOne',
                'titulo' => __('panel::panel.documentation.rel_guide_hasone_title'),
                'descripcion' => __('panel::panel.documentation.rel_guide_hasone_desc'),
                'codigo' => <<<'PHP'
// ProductResource::relations()
use MyLaravelTools\Panel\Relations\RelationManager;

RelationManager::hasOne('detail', ProductDetailResource::class)
    ->title('Ficha técnica'),

// Ruta: /admin/resources/product/{id} → pestaña «Ficha técnica»
PHP,
            ],
            [
                'id' => 'morphMany',
                'titulo' => __('panel::panel.documentation.rel_guide_morph_title'),
                'descripcion' => __('panel::panel.documentation.rel_guide_morph_desc'),
                'codigo' => <<<'PHP'
RelationManager::morphMany('reviews', ReviewResource::class)
    ->title('Reseñas de clientes'),

// El modelo Review usa morphTo('reviewable')
PHP,
            ],
            [
                'id' => 'belongsToMany',
                'titulo' => __('panel::panel.documentation.rel_guide_belongs_title'),
                'descripcion' => __('panel::panel.documentation.rel_guide_belongs_desc'),
                'codigo' => <<<'PHP'
RelationManager::belongsToMany('tags', TagResource::class)
    ->title('Etiquetas del producto'),

// Pivot: attach / detach desde la pestaña del registro padre
PHP,
            ],
        ];
    }
}
