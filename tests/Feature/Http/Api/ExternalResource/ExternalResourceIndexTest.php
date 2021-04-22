<?php

namespace Tests\Feature\Http\Api\ExternalResource;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ResourceSite;
use App\Http\Resources\ExternalResourceCollection;
use App\Http\Resources\ExternalResourceResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExternalResourceIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * By default, the Resource Index Endpoint shall return a collection of ExternalResource Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $resources = ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $response = $this->get(route('api.resource.index'));

        $response->assertJsonStructure([
            ExternalResourceCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Resource Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(ExternalResourceCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with($included_paths->all())->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'link',
            'external_id',
            'site',
            'as',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ExternalResourceResource::$wrap => $included_fields->join(','),
            ],
        ];

        $resources = ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowed_sorts = collect(ExternalResourceCollection::allowedSortFields());
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

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $builder = ExternalResource::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $resource = ExternalResource::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $resource = ExternalResource::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_resource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_resource->each(function ($resource) {
            $resource->delete();
        });

        $resource = ExternalResource::withoutTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_resource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_resource->each(function ($resource) {
            $resource->delete();
        });

        $resource = ExternalResource::withTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_resource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_resource->each(function ($resource) {
            $resource->delete();
        });

        $resource = ExternalResource::onlyTrashed()->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $resource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
            $resource->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $resource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
            $resource->each(function ($item) {
                $item->delete();
            });
        });

        $resource = ExternalResource::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resource, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support filtering by site.
     *
     * @return void
     */
    public function testSiteFilter()
    {
        $site_filter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $site_filter->key,
            ],
        ];

        ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::where('site', $site_filter->value)->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Resource Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.resource.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ExternalResourceCollection::make($resources, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
