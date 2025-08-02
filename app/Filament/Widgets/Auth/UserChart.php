<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Auth;

use App\Filament\Widgets\BaseChartWidget;
use App\Models\Auth\User;
use Flowframe\Trend\TrendValue;

class UserChart extends BaseChartWidget
{
    /**
     * Chart Id.
     */
    protected static ?string $chartId = 'userChart';

    /**
     * Get the displayed label of the widget.
     */
    protected function getHeading(): string
    {
        return __('filament.resources.label.users');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options.
     *
     * @return array<string, array>
     */
    protected function getOptions(): array
    {
        $data = $this->perMonth(User::class);

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
