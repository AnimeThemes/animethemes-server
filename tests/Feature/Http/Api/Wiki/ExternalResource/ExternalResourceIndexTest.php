<?php

declare(strict_types=1);

namespace Http\Api\Wiki\ExternalResource;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ExternalResourceIndexTest.
 */
class ExternalResourceIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

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
        $allowedPaths = collect(ExternalResourceCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->has(Artist::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with($includedPaths->all())->get();

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

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ExternalResourceResource::$wrap => $includedFields->join(','),
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
        $allowedSorts = collect(ExternalResourceCollection::allowedSortFields());
        $includedSorts = $allowedSorts->random($this->faker->numberBetween(1, count($allowedSorts)))->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $includedSorts->join(','),
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
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $resource = ExternalResource::where('created_at', $createdFilter)->get();

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
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $resource = ExternalResource::where('updated_at', $updatedFilter)->get();

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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteResource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteResource->each(function (ExternalResource $resource) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteResource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteResource->each(function (ExternalResource $resource) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteResource = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteResource->each(function (ExternalResource $resource) {
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
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            $resources = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
            $resources->each(function (ExternalResource $resource) {
                $resource->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $resources = ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();
            $resources->each(function (ExternalResource $resource) {
                $resource->delete();
            });
        });

        $resource = ExternalResource::withTrashed()->where('deleted_at', $deletedFilter)->get();

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
        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'site' => $siteFilter->key,
            ],
        ];

        ExternalResource::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::where('site', $siteFilter->value)->get();

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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        ExternalResource::factory()
            ->has(
                Anime::factory()
                ->count($this->faker->randomDigitNotNull)
                ->state([
                    'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $resources = ExternalResource::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
