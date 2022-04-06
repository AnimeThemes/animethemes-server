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
use Laravel\Nova\Card;
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
            (new NewAnime())->width(Card::ONE_HALF_WIDTH),
            (new AnimePerDay())->width(Card::ONE_HALF_WIDTH),

            (new NewArtists())->width(Card::ONE_HALF_WIDTH),
            (new ArtistsPerDay())->width(Card::ONE_HALF_WIDTH),

            (new NewSeries())->width(Card::ONE_HALF_WIDTH),
            (new SeriesPerDay())->width(Card::ONE_HALF_WIDTH),

            (new NewVideos())->width(Card::ONE_HALF_WIDTH),
            (new VideosPerDay())->width(Card::ONE_HALF_WIDTH),
        ];
    }
}
