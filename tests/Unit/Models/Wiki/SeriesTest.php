<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SeriesTest.
 */
class SeriesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $series = Series::factory()->create();

        static::assertIsString($series->searchableAs());
    }

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $series = Series::factory()->create();

        static::assertIsArray($series->toSearchableArray());
    }

    /**
     * Series shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $series = Series::factory()->create();

        static::assertEquals(1, $series->audits->count());
    }

    /**
     * Series shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $series = Series::factory()->create();

        static::assertIsString($series->getName());
    }

    /**
     * Series shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $animeCount = $this->faker->randomDigitNotNull;

        $series = Series::factory()
            ->has(Anime::factory()->count($animeCount))
            ->create();

        static::assertInstanceOf(BelongsToMany::class, $series->anime());
        static::assertEquals($animeCount, $series->anime()->count());
        static::assertInstanceOf(Anime::class, $series->anime()->first());
        static::assertEquals(AnimeSeries::class, $series->anime()->getPivotClass());
    }
}
