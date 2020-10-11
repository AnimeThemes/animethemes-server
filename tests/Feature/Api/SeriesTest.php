<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Series Index Endpoint shall display the Series attributes.
     *
     * @return void
     */
    public function testSeriesIndexAttributes()
    {
        $serie = Series::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.series.index'));

        $response->assertJson([
            'series' => $serie->map(function ($series) {
                return static::getData($series);
            })->toArray(),
        ]);
    }

    /**
     * The Show Series Endpoint shall display the Series attributes.
     *
     * @return void
     */
    public function testShowSeriesAttributes()
    {
        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(static::getData($series));
    }

    /**
     * The Show Series Endpoint shall display the anime relation in an 'anime' attribute.
     *
     * @return void
     */
    public function testShowSeriesAnimeAttributes()
    {
        $series = Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson([
            'anime' => $series->anime->map(function ($anime) {
                return AnimeTest::getData($anime);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Series resource.
     *
     * @param Series $series
     * @return array
     */
    public static function getData(Series $series)
    {
        return [
            'id' => $series->series_id,
            'name' => $series->name,
            'alias' => $series->alias,
            'created_at' => $series->created_at->toJSON(),
            'updated_at' => $series->updated_at->toJSON(),
        ];
    }
}
