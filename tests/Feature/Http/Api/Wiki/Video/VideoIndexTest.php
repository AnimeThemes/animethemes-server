<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $videos = Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.video.index'));

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
});

test('paginated', function () {
    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.video.index'));

    $response->assertJsonStructure([
        VideoCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new VideoSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->for(Audio::factory())
        ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with($includedPaths->all())->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('sparse fieldsets', function () {
    $schema = new VideoSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            VideoResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $videos = Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.video.index', $parameters));

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
});

test('sorts', function () {
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
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.video.index', $parameters));

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
});

test('created at filter', function () {
    $createdFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Video::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Video::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $video = Video::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('updated at filter', function () {
    $updatedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Video::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Video::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $video = Video::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('without trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Video::factory()->count(fake()->randomDigitNotNull())->create();

    Video::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $video = Video::withoutTrashed()->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('with trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Video::factory()->count(fake()->randomDigitNotNull())->create();

    Video::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $video = Video::withTrashed()->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('only trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Video::factory()->count(fake()->randomDigitNotNull())->create();

    Video::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $video = Video::onlyTrashed()->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('deleted at filter', function () {
    $deletedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            ModelConstants::ATTRIBUTE_DELETED_AT => $deletedFilter,
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($deletedFilter, function () {
        Video::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Video::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $video = Video::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('lyrics filter', function () {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('nc filter', function () {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_NC, $ncFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('overlap filter', function () {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('resolution filter', function () {
    $resolutionFilter = fake()->randomNumber();
    $excludedResolution = $resolutionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->state(new Sequence(
            [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
            [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
        ))
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('source filter', function () {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('subbed filter', function () {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_SUBBED, $subbedFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('uncen filter', function () {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $videos = Video::query()->where(Video::ATTRIBUTE_UNCEN, $uncenFilter)->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('entries by nsfw', function () {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with([
        Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($nsfwFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ])
        ->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('entries by spoiler', function () {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with([
        Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($spoilerFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ])
        ->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('entries by version', function () {
    $versionFilter = fake()->randomDigitNotNull();
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
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

    $response = get(route('api.video.index', $parameters));

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
});

test('themes by sequence', function () {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEME,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->state([
                            AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->boolean() ? $sequenceFilter : $excludedSequence,
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

    $response = get(route('api.video.index', $parameters));

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
});

test('themes by type', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEME,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with([
        Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('anime by media format', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with([
        Video::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('anime by season', function () {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->create();

    $videos = Video::with([
        Video::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.video.index', $parameters));

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
});

test('anime by year', function () {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    Video::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    AnimeTheme::factory()
                        ->for(
                            Anime::factory()
                                ->state([
                                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
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

    $response = get(route('api.video.index', $parameters));

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
});
