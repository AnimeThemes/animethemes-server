<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Anime;
use App\Nova\Filters\AnimeYearFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class AnimeYearTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Anime Year Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(AnimeYearFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Anime Year Filter shall have an option for each year between 1960 and the future year.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(AnimeYearFilter::class);

        for ($year = 1960; $year <= date('Y') + 1; $year++) {
            $filter->assertHasOption(strval($year));
        }
    }

    /**
     * The Anime Year Filter shall filter Anime By Year.
     *
     * @return void
     */
    public function testFilter()
    {
        $filterYear = $this->faker->numberBetween(1960, date('Y'));
        $excludedYear = $filterYear + 1;

        Anime::factory()
            ->count($this->faker->boolean() ? $filterYear : $excludedYear)
            ->create();

        $filter = $this->novaFilter(AnimeYearFilter::class);

        $response = $filter->apply(Anime::class, $filterYear);

        $filteredAnimes = Anime::where('year', $filterYear)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
