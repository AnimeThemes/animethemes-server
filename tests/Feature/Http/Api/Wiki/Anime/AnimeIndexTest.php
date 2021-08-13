<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\Anime\ThemeType;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme;
use App\Models\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnimeIndexTest.
 */
class AnimeIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Anime Index Endpoint shall return a collection of Anime Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $anime = Anime::factory()->count($this->faker->numberBetween(1, 3))->create();

        $response = $this->get(route('api.anime.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make())
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
    public function testPaginated()
    {
        $this->withoutEvents();

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
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(AnimeCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();
        $anime = Anime::with($includedPaths->all())->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'name',
            'slug',
            'year',
            'season',
            'synopsis',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                AnimeResource::$wrap => $includedFields->join(','),
            ],
        ];

        $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testSorts()
    {
        $this->withoutEvents();

        $allowedSorts = collect([
            'id',
            'name',
            'slug',
            'year',
            'season',
            'synopsis',
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

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $builder = Anime::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (AnimeCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($builder->get(), Query::make($parameters))
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
    public function testSeasonFilter()
    {
        $this->withoutEvents();

        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
        ];

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $anime = Anime::query()->where('season', $seasonFilter->value)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testYearFilter()
    {
        $this->withoutEvents();

        $yearFilter = $this->faker->numberBetween(2000, 2002);

        $parameters = [
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
        ];

        Anime::factory()
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                ['year' => 2000],
                ['year' => 2001],
                ['year' => 2002],
            ))
            ->create();

        $anime = Anime::query()->where('year', $yearFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $anime = Anime::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $anime = Anime::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::withoutTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::withTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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

        Anime::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAnime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAnime->each(function (Anime $anime) {
            $anime->delete();
        });

        $anime = Anime::onlyTrashed()->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
            $anime->each(function (Anime $item) {
                $item->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $anime = Anime::factory()->count($this->faker->randomDigitNotNull())->create();
            $anime->each(function (Anime $item) {
                $item->delete();
            });
        });

        $anime = Anime::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Index Endpoint shall support constrained eager loading of themes by group.
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
            IncludeParser::$param => 'themes',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['group' => $groupFilter],
                        ['group' => $excludedGroup],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testThemesBySequence()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'themes',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['sequence' => $sequenceFilter],
                        ['sequence' => $excludedSequence],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testThemesByType()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'themes',
        ];

        Anime::factory()
            ->has(Theme::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            'themes' => function (HasMany $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testEntriesByNsfw()
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'nsfw' => $nsfwFilter,
            ],
            IncludeParser::$param => 'themes.entries',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testEntriesBySpoiler()
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'spoiler' => $spoilerFilter,
            ],
            IncludeParser::$param => 'themes.entries',
        ];

        Anime::factory()
            ->has(
                Theme::factory()
                    ->has(Entry::factory()->count($this->faker->numberBetween(1, 3)))
                    ->count($this->faker->numberBetween(1, 3))
            )
            ->count($this->faker->numberBetween(1, 3))
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testEntriesByVersion()
    {
        $versionFilter = $this->faker->numberBetween(1, 3);
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
            IncludeParser::$param => 'themes.entries',
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->has(
                Theme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Entry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->state(new Sequence(
                                ['version' => $versionFilter],
                                ['version' => $excludedVersion],
                            ))
                    )
            )
            ->create();

        $anime = Anime::with([
            'themes.entries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testResourcesBySite()
    {
        $siteFilter = ResourceSite::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'site' => $siteFilter->description,
            ],
            IncludeParser::$param => 'resources',
        ];

        Anime::factory()
            ->has(ExternalResource::factory()->count($this->faker->randomDigitNotNull()), 'resources')
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            'resources' => function (BelongsToMany $query) use ($siteFilter) {
                $query->where('site', $siteFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testImagesByFacet()
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
            IncludeParser::$param => 'images',
        ];

        Anime::factory()
            ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $anime = Anime::with([
            'images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosByLyrics()
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'lyrics' => $lyricsFilter,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosByNc()
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'nc' => $ncFilter,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosByOverlap()
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'overlap' => $overlapFilter->description,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosByResolution()
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'resolution' => $resolutionFilter,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->has(
                Theme::factory()
                    ->count($this->faker->numberBetween(1, 3))
                    ->has(
                        Entry::factory()
                            ->count($this->faker->numberBetween(1, 3))
                            ->has(
                                Video::factory()
                                    ->count($this->faker->numberBetween(1, 3))
                                    ->state(new Sequence(
                                        ['resolution' => $resolutionFilter],
                                        ['resolution' => $excludedResolution],
                                    ))
                            )
                    )
            )
            ->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosBySource()
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'source' => $sourceFilter->description,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosBySubbed()
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'subbed' => $subbedFilter,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
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
    public function testVideosByUncen()
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'uncen' => $uncenFilter,
            ],
            IncludeParser::$param => 'themes.entries.videos',
        ];

        Anime::factory()->jsonApiResource()->count($this->faker->numberBetween(1, 3))->create();

        $anime = Anime::with([
            'themes.entries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.anime.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    AnimeCollection::make($anime, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
