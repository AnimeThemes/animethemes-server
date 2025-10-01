<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Wiki\Video;

use App\Filament\Widgets\BaseChartWidget;
use App\Models\Wiki\Video;
use Flowframe\Trend\TrendValue;

class VideoChart extends BaseChartWidget
{
    /**
     * Chart Id.
     */
    protected static ?string $chartId = 'videoChart';

    /**
     * Get the displayed label of the widget.
     */
    protected function getHeading(): string
    {
        return __('filament.resources.label.videos');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options.
     *
     * @return array<string, array>
     */
    protected function getOptions(): array
    {
        $data = $this->perMonth(Video::class);

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => $this->getHeading(),
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                ],
            ],
            'xaxis' => [
                'categories' => $data->map(fn (TrendValue $value): string => $this->translateDate($value->date)),
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
