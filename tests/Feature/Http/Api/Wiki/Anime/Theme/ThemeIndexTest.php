<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Anime\Theme;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ThemeType;
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
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ThemeIndexTest.
 */
class ThemeIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * By default, the Theme Index Endpoint shall return a collection of Theme Resources.
     *
     * @return void
     */
    public function testDefault()
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
                    ThemeCollection::make($themes, Query::make())
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
    public function testPaginated()
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
    public function testAllowedIncludePaths()
    {
        $allowedPaths = collect(ThemeCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(1, count($allowedPaths)));

        $parameters = [
            IncludeParser::$param => $includedPaths->join(','),
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
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testSparseFieldsets()
    {
        $fields = collect([
            'id',
            'type',
            'sequence',
            'group',
            'slug',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            FieldParser::$param => [
                ThemeResource::$wrap => $includedFields->join(','),
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
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testSorts()
    {
        $allowedSorts = collect([
            'id',
            'type',
            'sequence',
            'group',
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

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $builder = AnimeTheme::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (ThemeCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($builder->get(), Query::make($parameters))
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

        $theme = AnimeTheme::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, Query::make($parameters))
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

        $theme = AnimeTheme::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, Query::make($parameters))
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
                    ThemeCollection::make($theme, Query::make($parameters))
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
                    ThemeCollection::make($theme, Query::make($parameters))
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
                    ThemeCollection::make($theme, Query::make($parameters))
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

        $theme = AnimeTheme::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, Query::make($parameters))
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
    public function testGroupFilter()
    {
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['group' => $groupFilter],
                ['group' => $excludedGroup],
            ))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where('group', $groupFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testSequenceFilter()
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['sequence' => $sequenceFilter],
                ['sequence' => $excludedSequence],
            ))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where('sequence', $sequenceFilter)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testTypeFilter()
    {
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::query()->where('type', $typeFilter->value)->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testAnimeBySeason()
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'anime',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testImagesByFacet()
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'facet' => $facetFilter->description,
            ],
            IncludeParser::$param => 'anime.images',
        ];

        AnimeTheme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull()))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'anime.images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testEntriesByNsfw()
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'nsfw' => $nsfwFilter,
            ],
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(AnimeThemeEntry::factory()->count($this->faker->randomDigitNotNull()))
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
            IncludeParser::$param => 'animethemeentries',
        ];

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->state(new Sequence(
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'animethemeentries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosByLyrics()
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'lyrics' => $lyricsFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosByNc()
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'nc' => $ncFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosByOverlap()
    {
        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'overlap' => $overlapFilter->description,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosByResolution()
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'resolution' => $resolutionFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
                                ['resolution' => $resolutionFilter],
                                ['resolution' => $excludedResolution],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $themes = AnimeTheme::with([
            'animethemeentries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosBySource()
    {
        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            FilterParser::$param => [
                'source' => $sourceFilter->description,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosBySubbed()
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'subbed' => $subbedFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
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
    public function testVideosByUncen()
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::$param => [
                'uncen' => $uncenFilter,
            ],
            IncludeParser::$param => 'animethemeentries.videos',
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
            'animethemeentries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.animetheme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
