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

    public function testShowSeriesAttributes()
    {
        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(static::getData($series));
    }

    public function testShowSeriesAnimeAttributes()
    {
        $series = Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson([
            'anime' => $series->anime->map(function($anime) {
                return AnimeTest::getData($anime);
            })->toArray()
        ]);
    }

    public static function getData(Series $series) {
        return [
            'id' => $series->series_id,
            'name' => $series->name,
            'alias' => $series->alias,
            'created_at' => $series->created_at->toJSON(),
            'updated_at' => $series->updated_at->toJSON()
        ];
    }
}
