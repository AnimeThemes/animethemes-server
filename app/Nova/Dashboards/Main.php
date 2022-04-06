<?php

declare(strict_types=1);

namespace App\Nova\Dashboards;

use App\Nova\Metrics\Anime\AnimePerDay;
use App\Nova\Metrics\Anime\NewAnime;
use App\Nova\Metrics\Artist\ArtistsPerDay;
use App\Nova\Metrics\Artist\NewArtists;
use App\Nova\Metrics\Series\NewSeries;
use App\Nova\Metrics\Series\SeriesPerDay;
use App\Nova\Metrics\Video\NewVideos;
use App\Nova\Metrics\Video\VideosPerDay;
use Laravel\Nova\Dashboards\Main as Dashboard;

/**
 * Class Main.
 */
class Main extends Dashboard
{
    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function cards(): array
    {
        return [
            (new NewAnime())->width('1/2'),
            (new AnimePerDay())->width('1/2'),

            (new NewArtists())->width('1/2'),
            (new ArtistsPerDay())->width('1/2'),

            (new NewSeries())->width('1/2'),
            (new SeriesPerDay())->width('1/2'),

            (new NewVideos())->width('1/2'),
            (new VideosPerDay())->width('1/2'),
        ];
    }
}
