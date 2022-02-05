<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeSeriesTest.
 */
class AnimeSeriesTest extends TestCase
{
    use WithoutEvents;

    /**
     * An AnimeSeries shall belong to an Anime.
     *
     * @return void
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
     *
     * @return void
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
