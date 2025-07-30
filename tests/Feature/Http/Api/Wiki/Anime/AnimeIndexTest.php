<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
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
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
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

test('paginated', function () {
    Anime::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.anime.index'));

    $response->assertJsonStructure([
        AnimeCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new AnimeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

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

test('sparse fieldsets', function () {
    $schema = new AnimeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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

test('sorts', function () {
    $schema = new AnimeSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
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

test('season filter', function () {
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

test('media format filter', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
    ];

    Anime::factory()->count(fake()->randomDigitNotNull())->create();
    $anime = Anime::query()->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value)->get();

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

test('year filter', function () {
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
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
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
        Anime::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
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

test('without trashed filter', function () {
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

test('with trashed filter', function () {
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

test('only trashed filter', function () {
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
        Anime::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
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

test('synonyms by type', function () {
    $typeFilter = Arr::random(AnimeSynonymType::cases());

    $parameters = [
        FilterParser::param() => [
            SynonymResource::$wrap => [
                AnimeSynonym::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_SYNONYMS,
    ];

    Anime::factory()
        ->has(AnimeSynonym::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Anime::with([
        Anime::RELATION_SYNONYMS => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeSynonym::ATTRIBUTE_TYPE, $typeFilter->value);
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

test('themes by sequence', function () {
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
        Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
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

test('themes by type', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            ThemeResource::$wrap => [
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
        Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
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

test('entries by nsfw', function () {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
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

test('entries by spoiler', function () {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
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

test('entries by version', function () {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
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

test('resources by site', function () {
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
        Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
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

test('images by facet', function () {
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
        Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
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

test('videos by lyrics', function () {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
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

test('videos by nc', function () {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
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

test('videos by overlap', function () {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
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

test('videos by resolution', function () {
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
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
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

test('videos by source', function () {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
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

test('videos by subbed', function () {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
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

test('videos by uncen', function () {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    Anime::factory()->jsonApiResource()->count(fake()->numberBetween(1, 3))->create();

    $anime = Anime::with([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
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
