<?php

namespace Tests\Unit\Nova\Filters;

use App\Models\Anime;
use App\Nova\Filters\AnimeYearFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class AnimeYearTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

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
        $filter_year = $this->faker->numberBetween(1960, date('Y'));
        $excluded_year = $filter_year + 1;

        Anime::factory()
            ->count($this->faker->boolean() ? $filter_year : $excluded_year)
            ->create();

        $filter = $this->novaFilter(AnimeYearFilter::class);

        $response = $filter->apply(Anime::class, $filter_year);

        $filtered_animes = Anime::where('year', $filter_year)->get();
        foreach ($filtered_animes as $filtered_anime) {
            $response->assertContains($filtered_anime);
        }
        $response->assertCount($filtered_animes->count());
    }
}
