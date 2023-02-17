<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
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
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class ThemeIndexTest.
 */
class ThemeIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Theme Index Endpoint shall return a collection of Theme Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::all();

        $response = $this->get(route('api.animetheme.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animetheme.index'));

        $response->assertJsonStructure([
            ThemeCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Theme Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ThemeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with($includedPaths->all())->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ThemeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::all();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new ThemeSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $themes = $this->sort(AnimeTheme::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
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
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $theme = AnimeTheme::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
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
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $theme = AnimeTheme::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme->each(function (AnimeTheme $theme) {
            $theme->delete();
        });

        $theme = AnimeTheme::withoutTrashed()->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme->each(function (AnimeTheme $theme) {
            $theme->delete();
        });

        $theme = AnimeTheme::withTrashed()->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme = AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $deleteTheme->each(function (AnimeTheme $theme) {
            $theme->delete();
        });

        $theme = AnimeTheme::onlyTrashed()->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull())
                ->create();
        });

        $theme = AnimeTheme::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($theme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by group.
     *
     * @return void
     */
    public function testGroupFilter(): void
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_GROUP => $groupFilter,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                [AnimeTheme::ATTRIBUTE_GROUP => $groupFilter],
                [AnimeTheme::ATTRIBUTE_GROUP => $excludedGroup],
            ))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_GROUP, $groupFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by sequence.
     *
     * @return void
     */
    public function testSequenceFilter(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
            ))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support filtering by type.
     *
     * @return void
     */
    public function testTypeFilter(): void
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->description,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_ANIME,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_ANIME,
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_IMAGES,
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of entries by nsfw.
     *
     * @return void
     */
    public function testEntriesByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by spoiler.
     *
     * @return void
     */
    public function testEntriesBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by version.
     *
     * @return void
     */
    public function testEntriesByVersion(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by lyrics.
     *
     * @return void
     */
    public function testVideosByLyrics(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by nc.
     *
     * @return void
     */
    public function testVideosByNc(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap(): void
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->description,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by resolution.
     *
     * @return void
     */
    public function testVideosByResolution(): void
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(
                        Video::factory()
                            ->count($this->faker->randomDigitNotNull())
                            ->state(new Sequence(
                                [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource(): void
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->description,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by subbed.
     *
     * @return void
     */
    public function testVideosBySubbed(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Theme Index Endpoint shall support constrained eager loading of videos by uncen.
     *
     * @return void
     */
    public function testVideosByUncen(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ThemeCollection($themes, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
