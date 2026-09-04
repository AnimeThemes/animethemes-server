<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
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
use App\Http\Api\Schema\Wiki\ThemeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Resource\ThemeJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    Theme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->for(Song::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::all();

    $response = get(route('api.animetheme.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animetheme.index'));

    $response->assertJsonStructure([
        ThemeCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new ThemeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->for(Song::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with($includedPaths->all())->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ThemeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ThemeJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::all();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new ThemeSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animetheme.index', $parameters));

    $themes = $this->sort(Theme::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, $query)
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
            BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Date::withTestNow($createdFilter, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = Theme::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
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
            BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Date::withTestNow($updatedFilter, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = Theme::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('without trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = Theme::withoutTrashed()->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('with trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = Theme::withTrashed()->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('only trashed filter', function (): void {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    Theme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = Theme::onlyTrashed()->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('deleted at filter', function (): void {
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

    Date::withTestNow($deletedFilter, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Theme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = Theme::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sequence filter', function (): void {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Theme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->state(new Sequence(
            [Theme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
            [Theme::ATTRIBUTE_SEQUENCE => $excludedSequence],
        ))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::query()->where(Theme::ATTRIBUTE_SEQUENCE, $sequenceFilter)->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('type filter', function (): void {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            Theme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::query()->where(Theme::ATTRIBUTE_TYPE, $typeFilter->value)->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function (): void {
    $mediaFormatFilter = Arr::random(AnimeFormat::cases());

    $parameters = [
        FilterParser::param() => [
            'media_format' => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Theme::RELATION_ANIME,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function (): void {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Theme::RELATION_ANIME,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function (): void {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Theme::RELATION_ANIME,
    ];

    Theme::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('images by facet', function (): void {
    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => Theme::RELATION_IMAGES,
    ];

    Theme::factory()
        ->for(
            Anime::factory()
                ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter): void {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
            Entry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => Theme::RELATION_ENTRIES,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(Entry::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter): void {
            $query->where(Entry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
            Entry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => Theme::RELATION_ENTRIES,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(Entry::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter): void {
            $query->where(Entry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by version', function (): void {
    $versionFilter = fake()->randomDigitNotNull();
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Entry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => Theme::RELATION_ENTRIES,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [Entry::ATTRIBUTE_VERSION => $versionFilter],
                    [Entry::ATTRIBUTE_VERSION => $excludedVersion],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter): void {
            $query->where(Entry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter): void {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter): void {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter): void {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by resolution', function (): void {
    $resolutionFilter = fake()->randomNumber();
    $excludedResolution = $resolutionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    Video::factory()
                        ->count(fake()->randomDigitNotNull())
                        ->state(new Sequence(
                            [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                            [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                        ))
                )
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter): void {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter): void {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter): void {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
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
        IncludeParser::param() => Theme::RELATION_VIDEOS,
    ];

    Theme::factory()
        ->for(Anime::factory())
        ->has(
            Entry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = Theme::with([
        Theme::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter): void {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ])
        ->get();

    $response = get(route('api.animetheme.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeCollection($themes, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
