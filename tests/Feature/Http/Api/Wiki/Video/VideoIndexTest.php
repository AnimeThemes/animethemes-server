<?php

declare(strict_types=1);

namespace Http\Api\Wiki\Video;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\QueryParser;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class VideoIndexTest.
 */
class VideoIndexTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

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
        $allowedPaths = collect(VideoCollection::allowedIncludePaths());
        $includedPaths = $allowedPaths->random($this->faker->numberBetween(0, count($allowedPaths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $includedPaths->join(','),
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull)
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with($includedPaths->all())->get();

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

        $includedFields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                VideoResource::$wrap => $includedFields->join(','),
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

        $allowedSorts = collect(VideoCollection::allowedSortFields());
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
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $video = Video::where('created_at', $createdFilter)->get();

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
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            Video::factory()->count($this->faker->randomDigitNotNull)->create();
        });

        $video = Video::where('updated_at', $updatedFilter)->get();

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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteVideo->each(function (Video $video) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteVideo->each(function (Video $video) {
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
            Config::get('json-api-paginate.pagination_parameter') => [
                Config::get('json-api-paginate.size_parameter') => Config::get('json-api-paginate.max_results'),
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull)->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull)->create();
        $deleteVideo->each(function (Video $video) {
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
            $videos = Video::factory()->count($this->faker->randomDigitNotNull)->create();
            $videos->each(function (Video $video) {
                $video->delete();
            });
        });

        Carbon::withTestNow(Carbon::parse($excludedDate), function () {
            $videos = Video::factory()->count($this->faker->randomDigitNotNull)->create();
            $videos->each(function (Video $video) {
                $video->delete();
            });
        });

        $video = Video::withTrashed()->where('deleted_at', $deletedFilter)->get();

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

        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'lyrics' => $lyricsFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('lyrics', $lyricsFilter)->get();

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

        $ncFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nc' => $ncFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('nc', $ncFilter)->get();

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

        $overlapFilter = VideoOverlap::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'overlap' => $overlapFilter->key,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('overlap', $overlapFilter->value)->get();

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

        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'resolution' => $resolutionFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->state(new Sequence(
                ['resolution' => $resolutionFilter],
                ['resolution' => $excludedResolution],
            ))
            ->create();

        $videos = Video::where('resolution', $resolutionFilter)->get();

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

        $sourceFilter = VideoSource::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'source' => $sourceFilter->key,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('source', $sourceFilter->value)->get();

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

        $subbedFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'subbed' => $subbedFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('subbed', $subbedFilter)->get();

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

        $uncenFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'uncen' => $uncenFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $videos = Video::where('uncen', $uncenFilter)->get();

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
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'nsfw' => $nsfwFilter,
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
            'entries' => function (BelongsToMany $query) use ($nsfwFilter) {
                $query->where('nsfw', $nsfwFilter);
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
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'spoiler' => $spoilerFilter,
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
            'entries' => function (BelongsToMany $query) use ($spoilerFilter) {
                $query->where('spoiler', $spoilerFilter);
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
        $versionFilter = $this->faker->randomDigitNotNull;
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'version' => $versionFilter,
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
                        ['version' => $versionFilter],
                        ['version' => $excludedVersion],
                    ))
            )
            ->create();

        $videos = Video::with([
            'entries' => function (BelongsToMany $query) use ($versionFilter) {
                $query->where('version', $versionFilter);
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
        $groupFilter = $this->faker->word();
        $excludedGroup = $this->faker->word();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'group' => $groupFilter,
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
                                'group' => $this->faker->boolean() ? $groupFilter : $excludedGroup,
                            ])
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme' => function (BelongsTo $query) use ($groupFilter) {
                $query->where('group', $groupFilter);
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
        $sequenceFilter = $this->faker->randomDigitNotNull;
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'sequence' => $sequenceFilter,
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
                                'sequence' => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                            ])
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme' => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where('sequence', $sequenceFilter);
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
        $typeFilter = ThemeType::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'type' => $typeFilter->key,
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
            'entries.theme' => function (BelongsTo $query) use ($typeFilter) {
                $query->where('type', $typeFilter->value);
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
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'season' => $seasonFilter->key,
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
            'entries.theme.anime' => function (BelongsTo $query) use ($seasonFilter) {
                $query->where('season', $seasonFilter->value);
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
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            QueryParser::PARAM_FILTER => [
                'year' => $yearFilter,
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
                                        'year' => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                    )
            )
            ->create();

        $videos = Video::with([
            'entries.theme.anime' => function (BelongsTo $query) use ($yearFilter) {
                $query->where('year', $yearFilter);
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
