<?php

declare(strict_types=1);

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Wiki\Anime;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class AnimeQueryTest.
 */
class AnimeQueryTest extends TestCase
{
    use WithFaker;

    /**
     * The animeyears Query shall return a list of years.
     *
     * @return void
     */
    public function test_anime_years_query(): void
    {
        $years = Anime::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create()
            ->map(fn (Anime $anime) => $anime->getAttribute(Anime::ATTRIBUTE_YEAR))
            ->sortBy(fn (int $year) => $year)
            ->unique()
            ->values()
            ->toArray();

        $response = $this->graphQL('
        {
            animeyears
        }
        ');

        $response->assertJson([
            'data' => [
                'animeyears' => $years,
            ],
        ]);
    }
}
