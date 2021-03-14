<?php

namespace Tests\Unit\Pivots;

use App\Models\Anime;
use App\Models\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnimeSeriesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An AnimeSeries shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $anime_series = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_series->anime());
        $this->assertInstanceOf(Anime::class, $anime_series->anime()->first());
    }

    /**
     * An AnimeSeries shall belong to a Series.
     *
     * @return void
     */
    public function testSeries()
    {
        $anime_series = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->create();

        $this->assertInstanceOf(BelongsTo::class, $anime_series->series());
        $this->assertInstanceOf(Series::class, $anime_series->series()->first());
    }
}
