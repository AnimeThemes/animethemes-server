<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
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
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Group;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    AnimeTheme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->for(Song::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::all();

    $response = $this->get(route('api.animetheme.index'));

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

test('paginated', function () {
    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.animetheme.index'));

    $response->assertJsonStructure([
        ThemeCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new ThemeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->for(Group::factory())
        ->for(Song::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with($includedPaths->all())->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('sparse fieldsets', function () {
    $schema = new ThemeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::all();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('sorts', function () {
    $schema = new ThemeSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = $this->get(route('api.animetheme.index', $parameters));

    $themes = $this->sort(AnimeTheme::query(), $query, $schema)->get();

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
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = AnimeTheme::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = AnimeTheme::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('without trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = AnimeTheme::withoutTrashed()->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('with trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = AnimeTheme::withTrashed()->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('only trashed filter', function () {
    $parameters = [
        FilterParser::param() => [
            TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $theme = AnimeTheme::onlyTrashed()->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $theme = AnimeTheme::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('sequence filter', function () {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->state(new Sequence(
            [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
            [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
        ))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter)->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('type filter', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value)->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('anime by media format', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('anime by season', function () {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('anime by year', function () {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    AnimeTheme::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('images by facet', function () {
    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => AnimeTheme::RELATION_IMAGES,
    ];

    AnimeTheme::factory()
        ->for(
            Anime::factory()
                ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('entries by nsfw', function () {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('entries by spoiler', function () {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('entries by version', function () {
    $versionFilter = fake()->randomDigitNotNull();
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by lyrics', function () {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by nc', function () {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by overlap', function () {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by resolution', function () {
    $resolutionFilter = fake()->randomNumber();
    $excludedResolution = $resolutionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
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

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by source', function () {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by subbed', function () {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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

test('videos by uncen', function () {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $themes = AnimeTheme::with([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.animetheme.index', $parameters));

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
