<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SeriesTest.
 */
class SeriesTest extends TestCase
{
    use WithFaker;

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function test_searchable_as(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsString($series->searchableAs());
    }

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function test_to_searchable_array(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsArray($series->toSearchableArray());
    }

    /**
     * Series shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsString($series->getName());
    }

    /**
     * Series shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsString($series->getSubtitle());
    }

    /**
     * Series shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function test_anime(): void
    {
        $animeCount = $this->faker->randomDigitNotNull();

        $series = Series::factory()
            ->has(Anime::factory()->count($animeCount))
            ->createOne();

        static::assertInstanceOf(BelongsToMany::class, $series->anime());
        static::assertEquals($animeCount, $series->anime()->count());
        static::assertInstanceOf(Anime::class, $series->anime()->first());
        static::assertEquals(AnimeSeries::class, $series->anime()->getPivotClass());
    }
}
