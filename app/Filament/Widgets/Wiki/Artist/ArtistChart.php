<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Wiki\Artist;

use App\Filament\Widgets\BaseChartWidget;
use App\Models\Wiki\Artist;
use Flowframe\Trend\TrendValue;

/**
 * Class ArtistChart.
 */
class ArtistChart extends BaseChartWidget
{
    /**
     * Chart Id.
     *
     * @var string
     */
    protected static string $chartId = 'artistChart';

    /**
     * Get the displayed label of the widget.
     *
     * @return string
     */
    protected function getHeading(): string
    {
        return __('filament.resources.label.artists');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $data = $this->perMonth(Artist::class);

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => $this->getHeading(),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value) => $this->translateDate($value->date)),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#71E9C5'],
            'stroke' => [
                'curve' => 'smooth',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
