<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Support;

final class PanelExtensionesGuia
{
    /** @return list<array{id: string, titulo: string, descripcion: string, codigo: string}> */
    public static function pasos(): array
    {
        return [
            [
                'id' => 'campo',
                'titulo' => __('panel::panel.documentation.ext_guide_field_title'),
                'descripcion' => __('panel::panel.documentation.ext_guide_field_desc'),
                'codigo' => <<<'PHP'
// app/Panel/Resources/ProductResource.php
use MyLaravelTools\Panel\Fields\CustomField;

CustomField::make('rating', 'Valoración')
    ->type('rating')
    ->rules(['required', 'integer', 'min:1', 'max:5']);

// config/panel.php → extensions.field_views
'field_views' => [
    'rating' => 'panel.campo-rating',
],

// resources/views/panel/campo-rating.blade.php
// @props(['field', 'state' => ''])
<input type="range" min="1" max="5" wire:model="{{ $state }}" class="panel-input">
PHP,
            ],
            [
                'id' => 'columna',
                'titulo' => __('panel::panel.documentation.ext_guide_column_title'),
                'descripcion' => __('panel::panel.documentation.ext_guide_column_desc'),
                'codigo' => <<<'PHP'
// app/Panel/Resources/ProductResource.php
use MyLaravelTools\Panel\Columns\TextColumn;

TextColumn::make('rating', 'Valoración')->type('rating');

// config/panel.php → extensions.column_views
'column_views' => [
    'rating' => 'panel.columna-rating',
],

// resources/views/panel/columna-rating.blade.php
@php $valor = (int) ($value ?? 0); @endphp
<span class="panel-badge panel-badge-primary">{{ str_repeat('★', max(0, min(5, $valor))) }}</span>
PHP,
            ],
            [
                'id' => 'widget',
                'titulo' => __('panel::panel.documentation.ext_guide_widget_title'),
                'descripcion' => __('panel::panel.documentation.ext_guide_widget_desc'),
                'codigo' => <<<'PHP'
// app/Panel/Widgets/RatingPromedioWidget.php
use MyLaravelTools\Panel\Widgets\ViewWidget;

ViewWidget::make('Valoración media', 'panel.widget-rating', fn (): array => [
    'media' => 4.2,
    'total' => 128,
])->columnSpan(2);

// config/panel.php → widgets
App\Panel\Widgets\RatingPromedioWidget::definir(),

// O por código en AppServiceProvider:
// app(PanelExtensions::class)->registrarWidget($widget);
PHP,
            ],
        ];
    }
}
