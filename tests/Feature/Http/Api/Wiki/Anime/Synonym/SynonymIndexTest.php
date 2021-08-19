<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Synonym;

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
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SynonymIndexTest.
 */
class SynonymIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Synonym Index Endpoint shall return a collection of Synonym Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $synonyms = AnimeSynonym::all();

        $response = $this->get(route('api.animesynonym.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animesynonym.index'));

        $response->assertJsonStructure([
            SynonymCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Synonym Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(SynonymCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $synonyms = AnimeSynonym::with($includedPaths->all())->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'text',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                SynonymResource::$wrap => $includedFields->join(','),
            ],
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $synonyms = AnimeSynonym::all();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowedSorts = collect([
            'id',
            'text',
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

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $builder = AnimeSynonym::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (SynonymCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($builder->get(), Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
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
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $synonym = AnimeSynonym::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
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
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $synonym = AnimeSynonym::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym->each(function (AnimeSynonym $synonym) {
            $synonym->delete();
        });

        $synonym = AnimeSynonym::withoutTrashed()->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym->each(function (AnimeSynonym $synonym) {
            $synonym->delete();
        });

        $synonym = AnimeSynonym::withTrashed()->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $parameters = [
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteSynonym->each(function (AnimeSynonym $synonym) {
            $synonym->delete();
        });

        $synonym = AnimeSynonym::onlyTrashed()->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
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
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeSynonym::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $synonym = AnimeSynonym::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonym, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason()
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        AnimeSynonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $synonyms = AnimeSynonym::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear()
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'anime',
        ];

        AnimeSynonym::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $synonyms = AnimeSynonym::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animesynonym.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    SynonymCollection::make($synonyms, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}