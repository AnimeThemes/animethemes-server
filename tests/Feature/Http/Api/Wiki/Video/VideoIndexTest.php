<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Models\Wiki\AnimeSeason;
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
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.video.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make())
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
            ->count($this->faker->randomDigitNotNull())
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
            IncludeParser::$param => $includedPaths->join(','),
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(Theme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with($includedPaths->all())->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FieldParser::$param => [
                VideoResource::$wrap => $includedFields->join(','),
            ],
        ];

        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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

        $allowedSorts = collect([
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

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $builder = Video::query();

        foreach ($query->getSortCriteria() as $sortCriterion) {
            foreach (VideoCollection::sorts(collect([$sortCriterion])) as $sort) {
                $builder = $sort->applySort($builder);
            }
        }

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($builder->get(), Query::make($parameters))
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
            FilterParser::$param => [
                'created_at' => $createdFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $video = Video::query()->where('created_at', $createdFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'updated_at' => $updatedFilter,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $video = Video::query()->where('updated_at', $updatedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITHOUT,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteVideo->each(function (Video $video) {
            $video->delete();
        });

        $video = Video::withoutTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteVideo->each(function (Video $video) {
            $video->delete();
        });

        $video = Video::withTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'trashed' => TrashedStatus::ONLY,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteVideo = Video::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteVideo->each(function (Video $video) {
            $video->delete();
        });

        $video = Video::onlyTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'deleted_at' => $deletedFilter,
                'trashed' => TrashedStatus::WITH,
            ],
            PagingParser::$param => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $videos = Video::factory()->count($this->faker->randomDigitNotNull())->create();
            $videos->each(function (Video $video) {
                $video->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $videos = Video::factory()->count($this->faker->randomDigitNotNull())->create();
            $videos->each(function (Video $video) {
                $video->delete();
            });
        });

        $video = Video::withTrashed()->where('deleted_at', $deletedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($video, Query::make($parameters))
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
            FilterParser::$param => [
                'lyrics' => $lyricsFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('lyrics', $lyricsFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'nc' => $ncFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('nc', $ncFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'overlap' => $overlapFilter->description,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('overlap', $overlapFilter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'resolution' => $resolutionFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                ['resolution' => $resolutionFilter],
                ['resolution' => $excludedResolution],
            ))
            ->create();

        $videos = Video::query()->where('resolution', $resolutionFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'source' => $sourceFilter->description,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('source', $sourceFilter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'subbed' => $subbedFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('subbed', $subbedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'uncen' => $uncenFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where('uncen', $uncenFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'nsfw' => $nsfwFilter,
            ],
            IncludeParser::$param => 'entries',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'spoiler' => $spoilerFilter,
            ],
            IncludeParser::$param => 'entries',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'version' => $versionFilter,
            ],
            IncludeParser::$param => 'entries',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'group' => $groupFilter,
            ],
            IncludeParser::$param => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::$param => [
                'sequence' => $sequenceFilter,
            ],
            IncludeParser::$param => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'type' => $typeFilter->description,
            ],
            IncludeParser::$param => 'entries.theme',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'season' => $seasonFilter->description,
            ],
            IncludeParser::$param => 'entries.theme.anime',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
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
            FilterParser::$param => [
                'year' => $yearFilter,
            ],
            IncludeParser::$param => 'entries.theme.anime',
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                Entry::factory()
                    ->count($this->faker->randomDigitNotNull())
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
                    VideoCollection::make($videos, Query::make($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
