<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Enums\Http\Api\Field\Category;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        $schema = new EntrySchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

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
        $schema = new EntrySchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::$param => [
                EntryResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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
        $schema = new EntrySchema();

        $field = collect($schema->fields())
            ->filter(fn (Field $field) => $field->getCategory()->is(Category::ATTRIBUTE()))
            ->random();

        $parameters = [
            SortParser::$param => $field->getSort()->format(Direction::getRandomInstance()),
        ];

        $query = Query::make($parameters);

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $builder = AnimeThemeEntry::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach ($schema->sorts() as $sort) {
                $builder = $sort->applySort($sortCriterion, $builder);
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
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
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

        $entry = AnimeThemeEntry::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

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
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
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

        $entry = AnimeThemeEntry::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
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
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
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

        $entry = AnimeThemeEntry::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

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
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter)->get();

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
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter)->get();

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
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
            ))
            ->create();

        $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter)->get();

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
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::$param => AnimeThemeEntry::RELATION_ANIME,
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
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
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::$param => AnimeThemeEntry::RELATION_ANIME,
        ];

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
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
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
            IncludeParser::$param => AnimeThemeEntry::RELATION_THEME,
        ];

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_GROUP => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($groupFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter);
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
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::$param => AnimeThemeEntry::RELATION_THEME,
        ];

        AnimeThemeEntry::factory()
            ->for(
                AnimeTheme::factory()
                    ->for(Anime::factory())
                    ->state([
                        AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
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
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
            IncludeParser::$param => AnimeThemeEntry::RELATION_THEME,
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
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
