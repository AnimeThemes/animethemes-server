<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\SynonymType;
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
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Resource\ThemeJsonResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeJsonResource;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $anime = Anime::factory()->count(fake()->numberBetween(1, 3))->create();

    $response = get(route('api.anime.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.anime.index'));

    $response->assertJsonStructure([
        AnimeCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new AnimeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();
    $anime = Anime::with($includedPaths->all())->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new AnimeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $anime = Anime::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new AnimeSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.anime.index', $parameters));

    $anime = $this->sort(Anime::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('season filter', function (): void {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
    ];

    Anime::factory()->count(fake()->randomDigitNotNull())->create();
    $anime = Anime::query()->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('media format filter', function (): void {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
    ];

    Anime::factory()->count(fake()->randomDigitNotNull())->create();
    $anime = Anime::query()->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('year filter', function (): void {
    $yearFilter = fake()->numberBetween(2000, 2002);

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
    ];

    Anime::factory()
        ->count(fake()->randomDigitNotNull())
        ->state(new Sequence(
            [Anime::ATTRIBUTE_YEAR => 2000],
            [Anime::ATTRIBUTE_YEAR => 2001],
            [Anime::ATTRIBUTE_YEAR => 2002],
        ))
        ->create();

    $anime = Anime::query()->where(Anime::ATTRIBUTE_YEAR, $yearFilter)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $anime = Anime::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $anime = Anime::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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

    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $anime = Anime::withoutTrashed()->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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

    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $anime = Anime::withTrashed()->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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

    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $anime = Anime::onlyTrashed()->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $anime = Anime::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('synonyms by type', function (): void {
    $typeFilter = Arr::random(SynonymType::cases());

    $parameters = [
        FilterParser::param() => [
            SynonymJsonResource::$wrap => [
                Synonym::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_ANIMESYNONYMS,
    ];

    Anime::factory()
        ->has(Synonym::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_ANIMESYNONYMS => function (HasMany $query) use ($typeFilter): void {
            $query->where(Synonym::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by sequence', function (): void {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => Anime::RELATION_THEMES,
    ];

    Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by type', function (): void {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            ThemeJsonResource::$wrap => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_THEMES,
    ];

    Anime::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->has(AnimeThemeEntry::factory()->count(fake()->numberBetween(1, 3)))
                ->count(fake()->numberBetween(1, 3))
        )
        ->count(fake()->numberBetween(1, 3))
        ->create();

    $anime = Anime::with([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->has(AnimeThemeEntry::factory()->count(fake()->numberBetween(1, 3)))
                ->count(fake()->numberBetween(1, 3))
        )
        ->count(fake()->numberBetween(1, 3))
        ->create();

    $anime = Anime::with([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by version', function (): void {
    $versionFilter = fake()->numberBetween(1, 3);
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->has(
            AnimeTheme::factory()
                ->count(fake()->numberBetween(1, 3))
                ->has(
                    AnimeThemeEntry::factory()
                        ->count(fake()->numberBetween(1, 3))
                        ->state(new Sequence(
                            [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                            [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                        ))
                )
        )
        ->create();

    $anime = Anime::with([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('resources by site', function (): void {
    $siteFilter = Arr::random(ResourceSite::cases());

    $parameters = [
        FilterParser::param() => [
            ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_RESOURCES,
    ];

    Anime::factory()
        ->has(ExternalResource::factory()->count(fake()->randomDigitNotNull()), Anime::RELATION_RESOURCES)
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter): void {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_IMAGES,
    ];

    Anime::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter): void {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter): void {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter): void {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter): void {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()
        ->count(fake()->numberBetween(1, 3))
        ->has(
            AnimeTheme::factory()
                ->count(fake()->numberBetween(1, 3))
                ->has(
                    AnimeThemeEntry::factory()
                        ->count(fake()->numberBetween(1, 3))
                        ->has(
                            Video::factory()
                                ->count(fake()->numberBetween(1, 3))
                                ->state(new Sequence(
                                    [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                    [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                                ))
                        )
                )
        )
        ->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter): void {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter): void {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter): void {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter): void {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ])
        ->get();

    $response = get(route('api.anime.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
