<?php declare(strict_types=1);

namespace Nova\Filters;

use App\Models\Anime;
use App\Nova\Filters\AnimeYearFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class AnimeYearTest
 * @package Nova\Filters
 */
class AnimeYearTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Anime Year Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(AnimeYearFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Anime Year Filter shall have an option for each year between 1960 and the future year.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(AnimeYearFilter::class);

        for ($year = 1960; $year <= intval(date('Y')) + 1; $year++) {
            $filter->assertHasOption(strval($year));
        }
    }

    /**
     * The Anime Year Filter shall filter Anime By Year.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $filterYear = $this->faker->numberBetween(1960, date('Y'));
        $excludedYear = $filterYear + 1;

        Anime::factory()
            ->count($this->faker->boolean() ? $filterYear : $excludedYear)
            ->create();

        $filter = static::novaFilter(AnimeYearFilter::class);

        $response = $filter->apply(Anime::class, $filterYear);

        $filteredAnimes = Anime::where('year', $filterYear)->get();
        foreach ($filteredAnimes as $filteredAnime) {
            $response->assertContains($filteredAnime);
        }
        $response->assertCount($filteredAnimes->count());
    }
}
