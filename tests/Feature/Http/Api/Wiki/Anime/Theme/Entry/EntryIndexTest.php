<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme\Entry;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
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
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class EntryIndexTest.
 */
class EntryIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Entry Index Endpoint shall return a collection of Entry Resources.
     *
     * @return void
     */
    public function test_default(): void
    {
        $entries = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animethemeentry.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entries, new Query())
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
    public function test_paginated(): void
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
    public function test_allowed_include_paths(): void
    {
        $schema = new EntrySchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_sparse_fieldsets(): void
    {
        $schema = new EntrySchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_sorts(): void
    {
        $schema = new EntrySchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $entries = $this->sort(AnimeThemeEntry::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entries, $query)
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
    public function test_created_at_filter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
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
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_updated_at_filter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
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
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_without_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entry = AnimeThemeEntry::withoutTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_with_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entry = AnimeThemeEntry::withTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_only_trashed_filter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entry = AnimeThemeEntry::onlyTrashed()->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_deleted_at_filter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            AnimeThemeEntry::factory()
                ->trashed()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeThemeEntry::factory()
                ->trashed()
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $entry = AnimeThemeEntry::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entry, new Query($parameters))
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
    public function test_entries_by_nsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_entries_by_spoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_entries_by_version(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
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
                    new EntryCollection($entries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Entry Index Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function test_anime_by_media_format(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
        ];

        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $entries = AnimeThemeEntry::with([
            AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
            ->get();

        $response = $this->get(route('api.animethemeentry.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_anime_by_season(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_anime_by_year(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_themes_by_sequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
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
                    new EntryCollection($entries, new Query($parameters))
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
    public function test_themes_by_type(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
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
                    new EntryCollection($entries, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
