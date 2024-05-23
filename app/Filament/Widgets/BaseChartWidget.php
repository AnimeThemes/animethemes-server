<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\BaseModel;
use Flowframe\Trend\Trend;
use Illuminate\Support\Collection;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

/**
 * Class BaseChartWidget.
 */
class BaseChartWidget extends ApexChartWidget
{
    /**
     * Get the resources count created per month.
     * 
     * @param  class-string  $model
     * @return Collection
     */
    protected function perMonth(string $model): Collection
    {
        return Trend::model($model)
            ->between(now()->addMonths(-11)->startOfMonth(), now()->endOfMonth())
            ->perMonth()
            ->count();
    }

    /**
     * Translate the dates.
     *
     * @param  string  $date
     * @return string
     */
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

        return $dates[$month].' - '.$year;
    }
}