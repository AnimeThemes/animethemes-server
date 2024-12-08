<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Video;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Http\Resources\Wiki\Resource\VideoResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class VideoIndexTest.
 */
class VideoIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Video Index Endpoint shall return a collection of Video Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.video.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query())
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
    public function testPaginated(): void
    {
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
    public function testAllowedIncludePaths(): void
    {
        $schema = new VideoSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->for(Audio::factory())
            ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with($includedPaths->all())->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testSparseFieldsets(): void
    {
        $schema = new VideoSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                VideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testSorts(): void
    {
        $schema = new VideoSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.video.index', $parameters));

        $videos = $this->sort(Video::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, $query)
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
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $video = Video::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Video::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $video = Video::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        Video::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $video = Video::withoutTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        Video::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $video = Video::withTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        Video::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $video = Video::onlyTrashed()->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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
            Video::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Video::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $video = Video::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($video, new Query($parameters))
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
    public function testLyricsFilter(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testNcFilter(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_NC, $ncFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testOverlapFilter(): void
    {
        $overlapFilter = Arr::random(VideoOverlap::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testResolutionFilter(): void
    {
        $resolutionFilter = $this->faker->randomNumber();
        $excludedResolution = $resolutionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->state(new Sequence(
                [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
            ))
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testSourceFilter(): void
    {
        $sourceFilter = Arr::random(VideoSource::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testSubbedFilter(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_SUBBED, $subbedFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testUncenFilter(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $videos = Video::query()->where(Video::ATTRIBUTE_UNCEN, $uncenFilter)->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testEntriesByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testEntriesBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testEntriesByVersion(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();
        $excludedVersion = $versionFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
                    ->state(new Sequence(
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                        [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                    ))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testThemesBySequence(): void
    {
        $sequenceFilter = $this->faker->randomDigitNotNull();
        $excludedSequence = $sequenceFilter + 1;

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEME,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        AnimeTheme::factory()
                            ->for(Anime::factory())
                            ->state([
                                AnimeTheme::ATTRIBUTE_SEQUENCE => $this->faker->boolean() ? $sequenceFilter : $excludedSequence,
                            ])
                    )
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($sequenceFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testThemesByType(): void
    {
        $typeFilter = Arr::random(ThemeType::cases());

        $parameters = [
            FilterParser::param() => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
            IncludeParser::param() => Video::RELATION_ANIMETHEME,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($typeFilter) {
                $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Video Index Endpoint shall support constrained eager loading of anime by media format.
     *
     * @return void
     */
    public function testAnimeByMediaFormat(): void
    {
        $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
            ],
            IncludeParser::param() => Video::RELATION_ANIME,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
                $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testAnimeBySeason(): void
    {
        $seasonFilter = Arr::random(AnimeSeason::cases());

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
            ],
            IncludeParser::param() => Video::RELATION_ANIME,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(AnimeTheme::factory()->for(Anime::factory()))
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
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
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => Video::RELATION_ANIME,
        ];

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(
                AnimeThemeEntry::factory()
                    ->count($this->faker->randomDigitNotNull())
                    ->for(
                        AnimeTheme::factory()
                            ->for(
                                Anime::factory()
                                    ->state([
                                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                                    ])
                            )
                    )
            )
            ->create();

        $videos = Video::with([
            Video::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ])
        ->get();

        $response = $this->get(route('api.video.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new VideoCollection($videos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
