<?php

namespace Tests\Feature\Http\Api\Video;

use App\Enums\AnimeSeason;
use App\Enums\Filter\TrashedStatus;
use App\Enums\ThemeType;
use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class VideoIndexTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Video Index Endpoint shall return a collection of Video Resources.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.video.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated()
    {
        $this->withoutEvents();

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.video.index'));

        $response->assertJsonStructure([
            VideoCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Video Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $allowed_paths = collect(VideoCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with($included_paths->all())->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $fields = collect([
            'id',
            'basename',
            'filename',
            'path',
            'size',
            'mimetype',
            'resolution',
            'nc',
            'subbed',
            'lyrics',
            'uncen',
            'source',
            'overlap',
            'created_at',
            'updated_at',
            'deleted_at',
            'tags',
            'link',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                VideoResource::$wrap => $included_fields->join(','),
            ],
        ];

        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
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
        $this->withoutEvents();

        $allowed_sorts = collect(VideoCollection::allowedSortFields());
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

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $builder = Video::query();

        foreach ($parser->getSorts() as $field => $isAsc) {
            $builder = $builder->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
        }

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($builder->get(), QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter()
    {
        $this->withoutEvents();

        $created_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'created_at' => $created_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($created_filter), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $video = Video::where('created_at', $created_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter()
    {
        $this->withoutEvents();

        $updated_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'updated_at' => $updated_filter,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($updated_filter), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $video = Video::where('updated_at', $updated_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_video = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_video->each(function ($video) {
            $video->delete();
        });

        $video = Video::withoutTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_video = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_video->each(function ($video) {
            $video->delete();
        });

        $video = Video::withTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter()
    {
        $this->withoutEvents();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'trashed' => TrashedStatus::ONLY,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $delete_video = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $delete_video->each(function ($video) {
            $video->delete();
        });

        $video = Video::onlyTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter()
    {
        $this->withoutEvents();

        $deleted_filter = $this->faker->date();
        $excluded_date = $this->faker->date();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'deleted_at' => $deleted_filter,
                'trashed' => TrashedStatus::WITH,
            ],
        ];

        Carbon::withTestNow(Carbon::parse($deleted_filter), function () {
            $video = Video::factory()->count($this->faker->randomDigitNotNull)->create();
            $video->each(function ($item) {
                $item->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excluded_date), function () {
            $video = Video::factory()->count($this->faker->randomDigitNotNull)->create();
            $video->each(function ($item) {
                $item->delete();
            });
        });

        $video = Video::withTrashed()->where('deleted_at', $deleted_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by lyrics.
     *
     * @return void
     */
    public function testLyricsFilter()
    {
        $this->withoutEvents();

        $lyrics_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyrics_filter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('lyrics', $lyrics_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by nc.
     *
     * @return void
     */
    public function testNcFilter()
    {
        $this->withoutEvents();

        $nc_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $nc_filter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('nc', $nc_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by overlap.
     *
     * @return void
     */
    public function testOverlapFilter()
    {
        $this->withoutEvents();

        $overlap_filter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlap_filter->key,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('overlap', $overlap_filter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by resolution.
     *
     * @return void
     */
    public function testResolutionFilter()
    {
        $this->withoutEvents();

        $resolution_filter = $this->faker->randomNumber();
        $excluded_resolution = $resolution_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolution_filter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->state(new Sequence(
                ['resolution' => $resolution_filter],
                ['resolution' => $excluded_resolution],
            ))
            ->create();

        $videos = Video::where('resolution', $resolution_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by source.
     *
     * @return void
     */
    public function testSourceFilter()
    {
        $this->withoutEvents();

        $source_filter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $source_filter->key,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('source', $source_filter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by subbed.
     *
     * @return void
     */
    public function testSubbedFilter()
    {
        $this->withoutEvents();

        $subbed_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbed_filter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('subbed', $subbed_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support filtering by uncen.
     *
     * @return void
     */
    public function testUncenFilter()
    {
        $this->withoutEvents();

        $uncen_filter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncen_filter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('uncen', $uncen_filter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of entries by nsfw.
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

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            'entries' => function ($query) use ($nsfw_filter) {
                $query->where('nsfw', $nsfw_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of entries by spoiler.
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

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            'entries' => function ($query) use ($spoiler_filter) {
                $query->where('spoiler', $spoiler_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of entries by version.
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

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
                    ->state(new Sequence(
                        ['version' => $version_filter],
                        ['version' => $excluded_version],
                    ))
            )
            ->create();

        $videos = Video::with([
            'entries' => function ($query) use ($version_filter) {
                $query->where('version', $version_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of themes by group.
     *
     * @return void
     */
    public function testThemesByGroup()
    {
        $group_filter = $this->faker->word();
        $excluded_group = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $group_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'group' => $this->faker->boolean() ? $group_filter : $excluded_group,
                            ])
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme' => function ($query) use ($group_filter) {
                $query->where('group', $group_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of themes by sequence.
     *
     * @return void
     */
    public function testThemesBySequence()
    {
        $sequence_filter = $this->faker->randomDigitNotNull;
        $excluded_sequence = $sequence_filter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequence_filter,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(Anime::factory())
                            ->state([
                                'sequence' => $this->faker->boolean() ? $sequence_filter : $excluded_sequence,
                            ])
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme' => function ($query) use ($sequence_filter) {
                $query->where('sequence', $sequence_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of themes by type.
     *
     * @return void
     */
    public function testThemesByType()
    {
        $type_filter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $type_filter->key,
            ],
            QueryParser::PARAM_INCLUDE => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            'entries.theme' => function ($query) use ($type_filter) {
                $query->where('type', $type_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of anime by season.
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
            QueryParser::PARAM_INCLUDE => 'entries.theme.anime',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            'entries.theme.anime' => function ($query) use ($season_filter) {
                $query->where('season', $season_filter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of anime by year.
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
            QueryParser::PARAM_INCLUDE => 'entries.theme.anime',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(
                        Theme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        'year' => $this->faker->boolean() ? $year_filter : $excluded_year,
                                    ])
                            )
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme.anime' => function ($query) use ($year_filter) {
                $query->where('year', $year_filter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, QueryParser::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
