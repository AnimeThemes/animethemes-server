<?php

namespace Tests\Feature\Http\Api\Theme;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ImageFacet;
use App\Enums\ThemeType;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Http\Resources\ThemeCollection;
use App\Http\Resources\ThemeResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Image;
use App\Models\Song;
use App\Models\Theme;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ThemeIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        $allowed_paths = collect(ThemeCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
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

        $themes = Theme::with($included_paths->all())->get();

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

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                ThemeResource::$wrap => $included_fields->join(','),
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
        $allowed_sorts = collect(ThemeCollection::allowedSortFields());
        $included_sorts = $allowed_sorts->random($this->faker->numberBetween(1, count($allowed_sorts)))->map(function ($included_sort) {
            if ($this->faker->boolean()) {
                return Str::of('-')
                    ->append($included_sort)
                    ->__toString();
            }

            return $included_sort;
        });

        $parameters = [
            QueryParser::PARAM_SORT => $included_sorts->join(','),
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
        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::where('created_at', $created_filter)->get();

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
        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::where('updated_at', $updated_filter)->get();

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
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme->each(function ($theme) {
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
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme->each(function ($theme) {
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
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $delete_theme->each(function ($theme) {
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
        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Theme::factory()
                ->for(Anime::factory())
                ->count($this->faker->randomDigitNotNull)
                ->create();
        });

        $theme = Theme::withTrashed()->where('deleted_at', $deleted_filter)->get();

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
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['group' => $group_filter],
                ['group' => $excluded_group],
            ))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('group', $group_filter)->get();

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
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->state(new Sequence(
                ['sequence' => $sequence_filter],
                ['sequence' => $excluded_sequence],
            ))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('sequence', $sequence_filter)->get();

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
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::where('type', $type_filter->value)->get();

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
        $season_filter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $season_filter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
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
        $year_filter = intval($this->faker->year());
        $excluded_year = $year_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $year_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'anime',
        ];

        Theme::factory()
            ->for(
                Anime::factory()
                    ->state([
                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                    ])
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
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
        $facet_filter = ImageFacet::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'facet' => $facet_filter->key,
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
            'anime.images' => function ($query) use ($facet_filter) {
                $query->where('facet', $facet_filter->value);
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
        $nsfw_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfw_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function ($query) use ($nsfw_filter) {
                $query->where('nsfw', $nsfw_filter);
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
        $spoiler_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoiler_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function ($query) use ($spoiler_filter) {
                $query->where('spoiler', $spoiler_filter);
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
        $version_filter = $this->faker->randomDigitNotNull;
        $excluded_version = $version_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $version_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries',
        ];

        Theme::factory()
            ->for(Anime::factory())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->state(new Sequence(
                        ['version' => $version_filter],
                        ['version' => $excluded_version],
                    ))
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries' => function ($query) use ($version_filter) {
                $query->where('version', $version_filter);
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
        $lyrics_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyrics_filter,
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
            'entries.videos' => function ($query) use ($lyrics_filter) {
                $query->where('lyrics', $lyrics_filter);
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
        $nc_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $nc_filter,
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
            'entries.videos' => function ($query) use ($nc_filter) {
                $query->where('nc', $nc_filter);
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
        $overlap_filter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlap_filter->key,
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
            'entries.videos' => function ($query) use ($overlap_filter) {
                $query->where('overlap', $overlap_filter->value);
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
        $resolution_filter = $this->faker->randomNumber();
        $excluded_resolution = $resolution_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolution_filter,
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
                                ['resolution' => $resolution_filter],
                                ['resolution' => $excluded_resolution],
                            ))
                    )
            )
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $themes = Theme::with([
            'entries.videos' => function ($query) use ($resolution_filter) {
                $query->where('resolution', $resolution_filter);
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
        $source_filter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $source_filter->key,
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
            'entries.videos' => function ($query) use ($source_filter) {
                $query->where('source', $source_filter->value);
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
        $subbed_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbed_filter,
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
            'entries.videos' => function ($query) use ($subbed_filter) {
                $query->where('subbed', $subbed_filter);
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
        $uncen_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncen_filter,
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
            'entries.videos' => function ($query) use ($uncen_filter) {
                $query->where('uncen', $uncen_filter);
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
