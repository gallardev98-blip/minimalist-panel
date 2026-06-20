<?php

declare(strict_types=1);

namespace MyLaravelTools\Panel\Tests\Unit;

use MyLaravelTools\Panel\Relations\RelationManager;
use MyLaravelTools\Panel\Tests\Fixtures\ArticleResource;
use MyLaravelTools\Panel\Tests\TestCase;
use MyLaravelTools\Panel\Widgets\ChartWidget;
use MyLaravelTools\Panel\Widgets\ViewWidget;

final class Phase15FeaturesTest extends TestCase
{
    public function test_has_one_and_morph_factories(): void
    {
        $hasOne = RelationManager::hasOne('profile', ArticleResource::class);
        $morphMany = RelationManager::morphMany('comments', ArticleResource::class);
        $morphToMany = RelationManager::morphToMany('tags', ArticleResource::class);

        $this->assertTrue($hasOne->isHasOne());
        $this->assertTrue($morphMany->isMorphMany());
        $this->assertTrue($morphToMany->isMorphToMany());
        $this->assertTrue($morphToMany->isPivotRelation());
    }

    public function test_chart_widget_returns_data(): void
    {
        $widget = ChartWidget::make('Ventas', 'bar', [
            'labels' => ['Ene', 'Feb'],
            'values' => [10, 20],
        ]);

        $this->assertSame('bar', $widget->getChartType());
        $this->assertSame(['Ene', 'Feb'], $widget->getChartData()['labels']);
        $this->assertSame('30', $widget->getValue());
    }

    public function test_progression_resolves_to_line(): void
    {
        $widget = ChartWidget::make('Evolución', 'progression', ['labels' => ['Ene'], 'values' => [3]]);

        $this->assertTrue($widget->isProgression());
        $this->assertSame('line', $widget->resolveChartType());
    }

    public function test_chart_widget_custom_options(): void
    {
        $widget = ChartWidget::make('Test', 'doughnut', ['labels' => ['A'], 'values' => [1]])
            ->colors(['#fff'])
            ->height(200)
            ->options(['cutout' => '50%']);

        $this->assertSame('doughnut', $widget->getChartType());
        $this->assertSame(['#fff'], $widget->getColors());
        $this->assertSame(200, $widget->getHeight());
        $this->assertSame(['cutout' => '50%'], $widget->getChartOptions());
    }

    public function test_chart_widget_theme_colors(): void
    {
        $widget = ChartWidget::make('Estado', 'doughnut', [
            'labels' => ['Activos', 'Inactivos'],
            'values' => [8, 2],
        ])->themeColors();

        $this->assertSame(['success', 'danger'], $widget->getThemeColorKeys());
        $this->assertSame([], $widget->getColors());
    }

    public function test_view_widget_returns_view_and_data(): void
    {
        $widget = ViewWidget::make('Custom', 'panel.widgets.test', ['total' => 5])
            ->columnSpan(2);

        $this->assertSame('panel.widgets.test', $widget->getView());
        $this->assertSame(['total' => 5], $widget->getViewData());
        $this->assertSame(2, $widget->getColumnSpan());
    }
}
