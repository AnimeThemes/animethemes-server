<?php

declare(strict_types=1);

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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('paginated', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('allowed include paths', function () {
    $schema = new AnimeThemeEntryVideoSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('sparse fieldsets', function () {
    $schema = new AnimeThemeEntryVideoSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeThemeEntryVideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('sorts', function () {
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

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('created at filter', function () {
    $createdFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('updated at filter', function () {
    $updatedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('entries by nsfw', function () {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('entries by spoiler', function () {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('entries by version', function () {
    $versionFilter = fake()->randomDigitNotNull();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by lyrics', function () {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by nc', function () {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by overlap', function () {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by resolution', function () {
    $resolutionFilter = fake()->randomNumber();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by source', function () {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by subbed', function () {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});

test('videos by uncen', function () {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
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
});
