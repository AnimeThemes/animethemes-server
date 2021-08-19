<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Znck\Eloquent\Relations\BelongsToThrough;

/**
 * Class EntryIndexTest.
 */
class EntryIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Entry Index Endpoint shall return a collection of Entry Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $entries = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animethemeentry.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animethemeentry.index'));

        $response->assertJsonStructure([
            EntryCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Entry Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(EntryCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $entries = AnimeThemeEntry::with($includedPaths->all())->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'version',
            'episodes',
            'nsfw',
            'spoiler',
            'notes',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                EntryResource::$wrap => $includedFields->join(','),
            ],
        ];

        $entries = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts()
    {
        $allowedSorts = collect([
            'id',
            'version',
            'episodes',
            'nsfw',
            'spoiler',
            'notes',
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $builder = AnimeThemeEntry::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (EntryCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($builder->get(), Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by created_at.
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
            AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $entry = AnimeThemeEntry::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by updated_at.
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
            AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $entry = AnimeThemeEntry::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry->each(function (AnimeThemeEntry $entry) {
            $entry->delete();
        });

        $entry = AnimeThemeEntry::withoutTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry->each(function (AnimeThemeEntry $entry) {
            $entry->delete();
        });

        $entry = AnimeThemeEntry::withTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by trashed.
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteEntry->each(function (AnimeThemeEntry $entry) {
            $entry->delete();
        });

        $entry = AnimeThemeEntry::onlyTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by deleted_at.
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
            $entries = AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();

            $entries->each(function (AnimeThemeEntry $entry) {
                $entry->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $entries = AnimeThemeEntry::factory()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();

            $entries->each(function (AnimeThemeEntry $entry) {
                $entry->delete();
            });
        });

        $entry = AnimeThemeEntry::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entry, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by nsfw.
     *
     * @return void
     */
    public function testEntriesByNsfw()
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'nsfw' => $nsfwFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::query()->where('nsfw', $nsfwFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by spoiler.
     *
     * @return void
     */
    public function testEntriesBySpoiler()
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'spoiler' => $spoilerFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::query()->where('spoiler', $spoilerFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support filtering by version.
     *
     * @return void
     */
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                ['version' => $versionFilter],
                ['version' => $excludedVersion],
            ))
            ->create();

        $entries = AnimeThemeEntry::query()->where('version', $versionFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of anime by season.
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

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            'anime' => function (BelongsToThrough $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of anime by year.
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

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()->for(
                    Anime::factory()
                        ->state([
                            'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            'anime' => function (BelongsToThrough $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup()
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'animetheme',
        ];

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'group' => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            'animetheme' => function (BelongsTo $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'animetheme',
        ];

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        'sequence' => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            'animetheme' => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'animetheme',
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            'animetheme' => function (BelongsTo $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    EntryCollection::make($entries, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
