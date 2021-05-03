<?php

namespace Tests\Feature\Http\Api\Series;

use App\Enums\AnimeSeason;
use App\Http\Resources\SeriesResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeriesShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Series Show Endpoint shall return a Series Resource.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall return an Series Series for soft deleted seriess.
     *
     * @return void
     */
    public function testSoftDelete()
    {
        $this->withoutEvents();

        $series = Series::factory()->createOne();

        $series->delete();

        $series->unsetRelations();

        $response = $this->get(route('api.series.show', ['series' => $series]));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(SeriesResource::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $series = Series::with($included_paths->all())->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                SeriesResource::$wrap => $included_fields->join(','),
            ],
        ];

        $series = Series::factory()->create();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $this->withoutEvents();

        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $this->withoutEvents();

        $year_filter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['year' => 2000],
                        ['year' => 2001],
                        ['year' => 2002],
                    ))
            )
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->first();

        $response = $this->get(route('api.series.show', ['series' => $series] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesResource::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
