<?php

namespace Tests\Feature\Http\Api\Series;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Http\Resources\SeriesCollection;
use App\Http\Resources\SeriesResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

class SeriesIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Series Index Endpoint shall return a collection of Series Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $series = Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.series.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        $this->withoutEvents();

        Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.series.index'));

        $response->assertJsonStructure([
            SeriesCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Series Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(SeriesCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $series = Series::with($included_paths->all())->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall implement sparse fieldsets.
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
            'as',
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

        $series = Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $this->withoutEvents();

        $allowed_sorts = collect(SeriesCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
        ];

        $parser = QueryParser::make($parameters);

        Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = Series::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $this->withoutEvents();

        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Series::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Series::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $series = Series::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $this->withoutEvents();

        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Series::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Series::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $series = Series::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_series = Series::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_series->each(function ($series) {
            $series->delete();
        });

        $series = Series::withoutTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_series = Series::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_series->each(function ($series) {
            $series->delete();
        });

        $series = Series::withTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_series = Series::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_series->each(function ($series) {
            $series->delete();
        });

        $series = Series::onlyTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $this->withoutEvents();

        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $series = Series::factory()->count($this->faker->randomDigitNotNull)->create();
            $series->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $series = Series::factory()->count($this->faker->randomDigitNotNull)->create();
            $series->each(function ($item) {
                $item->delete();
            });
        });

        $series = Series::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Series Index Endpoint shall support constrained eager loading of anime by season.
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
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
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $series = Series::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
