<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class AnimeSeriesTest extends TestCase
{
    /**
     * An AnimeSeries shall belong to an Anime.
     */
    public function testAnime(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeSeries->anime());
        static::assertInstanceOf(Anime::class, $animeSeries->anime()->first());
    }

    /**
     * An AnimeSeries shall belong to a Series.
     */
    public function testSeries(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $animeSeries->series());
        static::assertInstanceOf(Series::class, $animeSeries->series()->first());
    }
}
