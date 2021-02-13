<?php

namespace Tests\Unit\Models;

use App\Models\Anime;
use App\Models\Series;
use App\Pivots\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $series = Series::factory()->create();

        $this->assertIsString($series->searchableAs());
    }

    /**
     * Series shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $series = Series::factory()->create();

        $this->assertIsArray($series->toSearchableArray());
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

        $this->assertEquals(1, $series->audits->count());
    }

    /**
     * Series shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $series = Series::factory()->create();

        $this->assertIsString($series->getName());
    }

    /**
     * Series shall have a many-to-many relationship with the type Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $anime_count = $this->faker->randomDigitNotNull;

        $series = Series::factory()
            ->has(Anime::factory()->count($anime_count))
            ->create();

        $this->assertInstanceOf(BelongsToMany::class, $series->anime());
        $this->assertEquals($anime_count, $series->anime()->count());
        $this->assertInstanceOf(Anime::class, $series->anime()->first());
        $this->assertEquals(AnimeSeries::class, $series->anime()->getPivotClass());
    }
}
