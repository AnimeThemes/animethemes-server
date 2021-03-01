<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\AnimeSeason;
use App\Models\Anime;
use App\Nova\Filters\AnimeSeasonFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class AnimeSeasonTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Anime Season Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(AnimeSeasonFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Anime Season Filter shall have an option for each AnimeSeason instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(AnimeSeasonFilter::class);

        foreach (AnimeSeason::getInstances() as $season) {
            $filter->assertHasOption($season->description);
        }
    }

    /**
     * The Anime Season Filter shall filter Anime By Season.
     *
     * @return void
     */
    public function testFilter()
    {
        $season = AnimeSeason::getRandomInstance();

        Anime::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(AnimeSeasonFilter::class);

        $response = $filter->apply(Anime::class, $season->value);

        $filtered_animes = Anime::where('season', $season->value)->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}