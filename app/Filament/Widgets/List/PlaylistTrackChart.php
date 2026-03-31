<?php

declare(strict_types=1);

namespace App\Filament\Widgets\List;

use App\Filament\Widgets\BaseChartWidget;
use App\Models\List\Playlist\PlaylistTrack;
use Flowframe\Trend\TrendValue;

class PlaylistTrackChart extends BaseChartWidget
{
    /**
     * Chart Id.
     */
    protected static ?string $chartId = 'playlistsTracksChart';

    /**
     * Get the displayed label of the widget.
     */
    protected function getHeading(): string
    {
        return __('filament.resources.label.playlist_tracks');
    }

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://echarts.apache.org/en/option.html.
     */
    protected function getOptions(): array
    {
        $data = $this->perMonth(PlaylistTrack::class);

        return [
            'grid' => [
                'top' => '10',
                'left' => '0',
                'bottom' => '0',
                'right' => '0',
                'containLabel' => true,
            ],
            'xAxis' => [
                'type' => 'category',
                'boundaryGap' => true,
                'data' => $data->map(fn (TrendValue $value): string => $this->translateDate($value->date)),
                'axisLabel' => [
                    'interval' => 0,
                ],
            ],
            'yAxis' => [
                'type' => 'value',

            ],
            'series' => [
                [
                    'data' => $data->map(fn (TrendValue $value): mixed => $value->aggregate),
                    'type' => 'bar',
                    'label' => [
                        'show' => true,
                    ],
                ],
            ],
        ];
    }
}
