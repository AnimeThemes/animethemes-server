<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Elemind\FilamentECharts\Widgets\EChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BaseChartWidget extends EChartWidget
{
    protected static bool $isLazy = false;

    /**
     * Get the resources count created per month.
     *
     * @param  class-string  $model
     */
    protected function perMonth(string $model): Collection
    {
        return Cache::flexible("filament_chart_$model", [300, 1200], fn (): Collection => Trend::model($model)
            ->between(now()->subMonths(11)->startOfMonth(), now()->endOfMonth())
            ->perMonth()
            ->count());
    }

    protected function translateDate(string $date): string
    {
        $month = intval(explode('-', $date)[1]);
        $year = substr(explode('-', $date)[0], 2);

        $dates = [
            1 => __('filament.widgets.month.jan'),
            2 => __('filament.widgets.month.feb'),
            3 => __('filament.widgets.month.mar'),
            4 => __('filament.widgets.month.apr'),
            5 => __('filament.widgets.month.may'),
            6 => __('filament.widgets.month.jun'),
            7 => __('filament.widgets.month.jul'),
            8 => __('filament.widgets.month.aug'),
            9 => __('filament.widgets.month.sep'),
            10 => __('filament.widgets.month.oct'),
            11 => __('filament.widgets.month.nov'),
            12 => __('filament.widgets.month.dec'),
        ];

        return $dates[$month].'/'.$year;
    }
}
