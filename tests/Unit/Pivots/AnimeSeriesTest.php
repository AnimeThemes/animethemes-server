<?php

declare(strict_types=1);

namespace Pivots;

use App\Models\Anime;
use App\Models\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeSeriesTest
 * @package Pivots
 */
class AnimeSeriesTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

    /**
     * An AnimeSeries shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->create();

        static::assertInstanceOf(BelongsTo::class, $animeSeries->anime());
        static::assertInstanceOf(Anime::class, $animeSeries->anime()->first());
    }

    /**
     * An AnimeSeries shall belong to a Series.
     *
     * @return void
     */
    public function testSeries()
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->create();

        static::assertInstanceOf(BelongsTo::class, $animeSeries->series());
        static::assertInstanceOf(Series::class, $animeSeries->series()->first());
    }
}
