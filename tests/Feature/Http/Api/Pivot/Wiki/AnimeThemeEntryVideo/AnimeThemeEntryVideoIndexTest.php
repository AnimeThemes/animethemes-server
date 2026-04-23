<?php

declare(strict_types=1);

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
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $entryVideos = AnimeThemeEntryVideo::all();

    $response = get(route('api.animethemeentryvideo.index'));

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

test('paginated', function (): void {
    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index'));

    $response->assertJsonStructure([
        AnimeThemeEntryVideoCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new AnimeThemeEntryVideoSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

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

test('sparse fieldsets', function (): void {
    $schema = new AnimeThemeEntryVideoSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeThemeEntryVideoJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

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

test('sorts', function (): void {
    $schema = new AnimeThemeEntryVideoSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

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

test('created at filter', function (): void {
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

    Date::withTestNow($createdFilter, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    Date::withTestNow($excludedDate, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    $entryVideos = AnimeThemeEntryVideo::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.animethemeentryvideo.index', $parameters));

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

test('updated at filter', function (): void {
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

    Date::withTestNow($updatedFilter, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    Date::withTestNow($excludedDate, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            AnimeThemeEntryVideo::factory()
                ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
                ->for(Video::factory())
                ->create();
        });
    });

    $entryVideos = AnimeThemeEntryVideo::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.animethemeentryvideo.index', $parameters));

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

test('entries by nsfw', function (): void {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($nsfwFilter): void {
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

test('entries by spoiler', function (): void {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($spoilerFilter): void {
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

test('entries by version', function (): void {
    $versionFilter = fake()->randomDigitNotNull();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($versionFilter): void {
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

test('videos by lyrics', function (): void {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter): void {
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

test('videos by nc', function (): void {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter): void {
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

test('videos by overlap', function (): void {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter): void {
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

test('videos by resolution', function (): void {
    $resolutionFilter = fake()->randomNumber();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter): void {
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

test('videos by source', function (): void {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter): void {
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

test('videos by subbed', function (): void {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter): void {
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

test('videos by uncen', function (): void {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->create();
    });

    $response = get(route('api.animethemeentryvideo.index', $parameters));

    $entryVideos = AnimeThemeEntryVideo::with([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter): void {
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
