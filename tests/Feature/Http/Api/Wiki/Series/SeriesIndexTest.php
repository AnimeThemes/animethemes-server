<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SeriesIndexTest.
 */
class SeriesIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Series Index Endpoint shall return a collection of Series Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $series = Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.series.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make())
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

        Series::factory()->count($this->faker->randomDigitNotNull())->create();

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
        $allowedPaths = collect(SeriesCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $series = Series::with($includedPaths->all())->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                SeriesResource::$wrap => $includedFields->join(','),
            ],
        ];

        $series = Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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

        $allowedSorts = collect([
            'id',
            'name',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $sortCount = $this->faker->numberBetween(1, count($allowedSorts));

        $includedSorts = $allowedSorts->random($sortCount)->map(function (string $includedSort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($includedSort)
                    ->__toString();
            }

            return $includedSort;
        });

        $parameters = [
            SortParser::$param => $includedSorts->join(','),
        ];

        $query = Query::make($parameters);

        Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Series::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (SeriesCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($builder->get(), Query::make($parameters))
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

        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'created_at' => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Series::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Series::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $series = Series::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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

        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'updated_at' => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Series::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Series::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $series = Series::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSeries = Series::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSeries->each(function (Series $series) {
            $series->delete();
        });

        $series = Series::withoutTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSeries = Series::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSeries->each(function (Series $series) {
            $series->delete();
        });

        $series = Series::withTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Series::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteSeries = Series::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteSeries->each(function (Series $series) {
            $series->delete();
        });

        $series = Series::onlyTrashed()->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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

        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::$param => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $series = Series::factory()->count($this->faker->randomDigitNotNull())->create();
            $series->each(function (Series $item) {
                $item->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $series = Series::factory()->count($this->faker->randomDigitNotNull())->create();
            $series->each(function (Series $item) {
                $item->delete();
            });
        });

        $series = Series::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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

        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        Series::factory()
            ->has(Anime::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $series = Series::with([
            'anime' => function (BelongsToMany $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
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

        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        Series::factory()
            ->has(
                Anime::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['year' => 2000],
                        ['year' => 2001],
                        ['year' => 2002],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $series = Series::with([
            'anime' => function (BelongsToMany $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.series.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SeriesCollection::make($series, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
