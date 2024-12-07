<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
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
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class AnimeIndexTest.
 */
class AnimeIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Anime Index Endpoint shall return a collection of Anime Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $anime = Anime::factory()->count($this->faker->numberBetween(1, 3))->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJsonStructure([
            AnimeCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();
        $anime = Anime::with($includedPaths->all())->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AnimeSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.anime.index', $parameters));

        $anime = $this->sort(Anime::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by season.
     *
     * @return void
     */
    public function testSeasonFilter(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $anime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by media_format.
     *
     * @return void
     */
    public function testMediaFormatFilter(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $anime = Anime::query()->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by season.
     *
     * @return void
     */
    public function testYearFilter(): void
    {
        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
        ];

        Anime::factory()
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                [Anime::ATTRIBUTE_YEAR => 2000],
                [Anime::ATTRIBUTE_YEAR => 2001],
                [Anime::ATTRIBUTE_YEAR => 2002],
            ))
            ->create();

        $anime = Anime::query()->where(Anime::ATTRIBUTE_YEAR, $yearFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by created_at.
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
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $anime = Anime::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by updated_at.
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
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $anime = Anime::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        Anime::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $anime = Anime::withoutTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        Anime::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $anime = Anime::withTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        Anime::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $anime = Anime::onlyTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support filtering by deleted_at.
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
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            Anime::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Anime::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $anime = Anime::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of synonyms by type.
     *
     * @return void
     */
    public function testSynonymsByType(): void
    {
        $typeFilter = Arr::random(AnimeSynonymType::cases());

        $parameters = [
            FilterParser::param() => [
                SynonymResource::$wrap => [
                    AnimeSynonym::ATTRIBUTE_TYPE => $typeFilter->localize(),
                ],
            ],
            IncludeParser::param() => Anime::RELATION_SYNONYMS,
        ];

        Anime::factory()
            ->has(AnimeSynonym::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            Anime::RELATION_SYNONYMS => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeSynonym::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Anime::RELATION_THEMES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                        [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                ThemeResource::$wrap => [
                    AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
                ],
            ],
            IncludeParser::param() => Anime::RELATION_THEMES,
        ];

        Anime::factory()
            ->has(AnimeTheme::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of entries by nsfw.
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
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
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
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->has(
                AnimeTheme::factory()
                    ->has(AnimeThemeEntry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
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
        $versionFilter = $this->faker->numberBetween(1, 3);
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => Anime::RELATION_ENTRIES,
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                                [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                            ))
                    )
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of resources by site.
     *
     * @return void
     */
    public function testResourcesBySite(): void
    {
        $siteFilter = Arr::random(ResourceSite::cases());

        $parameters = [
            FilterParser::param() => [
                ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
            ],
            IncludeParser::param() => Anime::RELATION_RESOURCES,
        ];

        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), Anime::RELATION_RESOURCES)
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
                $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = Arr::random(ImageFacet::cases());

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->localize(),
            ],
            IncludeParser::param() => Anime::RELATION_IMAGES,
        ];

        Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by lyrics.
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
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by nc.
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
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by overlap.
     *
     * @return void
     */
    public function testVideosByOverlap(): void
    {
        $overlapFilter = Arr::random(VideoOverlap::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by resolution.
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
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->has(
                AnimeTheme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        AnimeThemeEntry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(
                                Video::factory()
                                    ->count($this->faker->numberBetween(1, 3))
                                    ->state(new Sequence(
                                        [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                        [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                                    ))
                            )
                    )
            )
            ->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by source.
     *
     * @return void
     */
    public function testVideosBySource(): void
    {
        $sourceFilter = Arr::random(VideoSource::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
            ],
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by subbed.
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
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of videos by uncen.
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
            IncludeParser::param() => Anime::RELATION_VIDEOS,
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeCollection($anime, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
