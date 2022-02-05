<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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
    public function testSearchableAs(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsString($series->searchableAs());
    }

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsArray($series->toSearchableArray());
    }

    /**
     * Series shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $series = Series::factory()->createOne();

        static::assertEquals(1, $series->audits()->count());
    }

    /**
     * Series shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $series = Series::factory()->createOne();

        static::assertIsString($series->getName());
    }

    /**
     * Series shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime(): void
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
