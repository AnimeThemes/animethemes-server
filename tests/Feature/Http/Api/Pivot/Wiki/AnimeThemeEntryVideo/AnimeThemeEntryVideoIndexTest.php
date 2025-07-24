<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\AnimeThemeEntryVideo;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
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
use App\Http\Api\Schema\Pivot\Wiki\AnimeThemeEntryVideoSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeThemeEntryVideoCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AnimeThemeEntryVideoIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Anime Theme Entry Video Index Endpoint shall return a collection of Anime Theme Entry Video Resources.
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $entryVideos = AnimeThemeEntryVideo::all();

        $response = $this->get(route('api.animethemeentryvideo.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall be paginated.
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index'));

        $response->assertJsonStructure([
            AnimeThemeEntryVideoCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeThemeEntryVideoSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeThemeEntryVideoSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeThemeEntryVideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall support sorting resources.
     */
    public function testSorts(): void
    {
        $schema = new AnimeThemeEntryVideoSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = $this->sort(AnimeThemeEntryVideo::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall support filtering by created_at.
     */
    public function testCreatedAtFilter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeThemeEntryVideo::factory()
                    ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                    ->for(Video::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeThemeEntryVideo::factory()
                    ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                    ->for(Video::factory())
                    ->create();
            });
        });

        $entryVideos = AnimeThemeEntryVideo::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Index Endpoint shall support filtering by updated_at.
     */
    public function testUpdatedAtFilter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeThemeEntryVideo::factory()
                    ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                    ->for(Video::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                AnimeThemeEntryVideo::factory()
                    ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                    ->for(Video::factory())
                    ->create();
            });
        });

        $entryVideos = AnimeThemeEntryVideo::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by nsfw.
     */
    public function testEntriesByNsfw(): void
    {
        $nsfwFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($nsfwFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by spoiler.
     */
    public function testEntriesBySpoiler(): void
    {
        $spoilerFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($spoilerFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of entries by version.
     */
    public function testEntriesByVersion(): void
    {
        $versionFilter = $this->faker->randomDigitNotNull();

        $parameters = [
            FilterParser::param() => [
                AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($versionFilter) {
                $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by lyrics.
     */
    public function testVideosByLyrics(): void
    {
        $lyricsFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_LYRICS => $lyricsFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter) {
                $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by nc.
     */
    public function testVideosByNc(): void
    {
        $ncFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_NC => $ncFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter) {
                $query->where(Video::ATTRIBUTE_NC, $ncFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by overlap.
     */
    public function testVideosByOverlap(): void
    {
        $overlapFilter = Arr::random(VideoOverlap::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter) {
                $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by resolution.
     */
    public function testVideosByResolution(): void
    {
        $resolutionFilter = $this->faker->randomNumber();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter) {
                $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by source.
     */
    public function testVideosBySource(): void
    {
        $sourceFilter = Arr::random(VideoSource::cases());

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter) {
                $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by subbed.
     */
    public function testVideosBySubbed(): void
    {
        $subbedFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_SUBBED => $subbedFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter) {
                $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Theme Entry Video Show Endpoint shall support constrained eager loading of videos by uncen.
     */
    public function testVideosByUncen(): void
    {
        $uncenFilter = $this->faker->boolean();

        $parameters = [
            FilterParser::param() => [
                Video::ATTRIBUTE_UNCEN => $uncenFilter,
            ],
            IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });

        $response = $this->get(route('api.animethemeentryvideo.index', $parameters));

        $entryVideos = AnimeThemeEntryVideo::with([
            AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter) {
                $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnimeThemeEntryVideoCollection($entryVideos, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
