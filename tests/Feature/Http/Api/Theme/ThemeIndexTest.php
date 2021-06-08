<?php

declare(strict_types=1);

namespace Http\Api\Theme;

use App\Enums\AnimeSeason;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\ImageFacet;
use App\Enums\ThemeType;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Http\Api\QueryParser;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
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
        Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::all();

        $response = $this->get(route('api.theme.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make())
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
        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.theme.index'));

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
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with($includedPaths->all())->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FIELDS => [
                ThemeResource::$wrap => $includedFields->join(','),
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::all();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
        $allowedSorts = collect(ThemeCollection::allowedSortFields());
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

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $builder = Theme::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($builder->get(), QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'created_at' => $createdFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($createdFilter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updatedFilter,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updatedFilter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme->each(function (Theme $theme) {
            $theme->delete();
        });

        $theme = Theme::withoutTrashed()->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme->each(function (Theme $theme) {
            $theme->delete();
        });

        $theme = Theme::withTrashed()->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $deleteTheme->each(function (Theme $theme) {
            $theme->delete();
        });

        $theme = Theme::onlyTrashed()->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deletedFilter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($theme, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['group' => $groupFilter],
                ['group' => $excludedGroup],
            ))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('group', $groupFilter)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
        $sequenceFilter = $this->faker->randomDigitNotNull;
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['sequence' => $sequenceFilter],
                ['sequence' => $excludedSequence],
            ))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('sequence', $sequenceFilter)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('type', $typeFilter->value)->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'facet' => $facetFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime.images',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->has(Image::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'anime.images' => function (BelongsToMany $query) use ($facetFilter) {
                $query->where('facet', $facetFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfwFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function (HasMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoilerFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function (HasMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
        $versionFilter = $this->faker->randomDigitNotNull;
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function (HasMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyricsFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($lyricsFilter) {
                $query->where('lyrics', $lyricsFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'nc' => $ncFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($ncFilter) {
                $query->where('nc', $ncFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlapFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($overlapFilter) {
                $query->where('overlap', $overlapFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolutionFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(
                        Video::factory()
                            ->count($this->faker->randomDigitNotNull)
                            ->state(new Sequence(
                                ['resolution' => $resolutionFilter],
                                ['resolution' => $excludedResolution],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($resolutionFilter) {
                $query->where('resolution', $resolutionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'source' => $sourceFilter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($sourceFilter) {
                $query->where('source', $sourceFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbedFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($subbedFilter) {
                $query->where('subbed', $subbedFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
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
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncenFilter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.videos',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function (BelongsToMany $query) use ($uncenFilter) {
                $query->where('uncen', $uncenFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.theme.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    ThemeCollection::make($themes, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
