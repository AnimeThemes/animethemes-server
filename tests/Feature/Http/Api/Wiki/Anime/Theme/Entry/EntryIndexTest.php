<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
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
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $entries = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animethemeentry.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animethemeentry.index'));

    $response->assertJsonStructure([
        EntryCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new EntrySchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        ->create();

    $entries = AnimeThemeEntry::with($includedPaths->all())->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new EntrySchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            EntryJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $entries = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new EntrySchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.animethemeentry.index', $parameters));

    $entries = $this->sort(AnimeThemeEntry::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, $query)
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
        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $entry = AnimeThemeEntry::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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
        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $entry = AnimeThemeEntry::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entry = AnimeThemeEntry::withoutTrashed()->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entry = AnimeThemeEntry::withTrashed()->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    AnimeThemeEntry::factory()
        ->trashed()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entry = AnimeThemeEntry::onlyTrashed()->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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
        AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        AnimeThemeEntry::factory()
            ->trashed()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $entry = AnimeThemeEntry::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entry, new Query($parameters))
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
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->state(new Sequence(
            [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
            [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
        ))
        ->create();

    $entries = AnimeThemeEntry::query()->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter)->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function (): void {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::with([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::with([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_ANIME,
    ];

    AnimeThemeEntry::factory()
        ->for(
            AnimeTheme::factory()->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::with([
        AnimeThemeEntry::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
        IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
    ];

    AnimeThemeEntry::factory()
        ->for(
            AnimeTheme::factory()
                ->for(Anime::factory())
                ->state([
                    AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->boolean() ? $sequenceFilter : $excludedSequence,
                ])
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::with([
        AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($sequenceFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ])
        ->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
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
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntry::RELATION_THEME,
    ];

    AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $entries = AnimeThemeEntry::with([
        AnimeThemeEntry::RELATION_THEME => function (BelongsTo $query) use ($typeFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.animethemeentry.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new EntryCollection($entries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
