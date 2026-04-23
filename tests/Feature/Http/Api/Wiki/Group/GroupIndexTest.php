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
use App\Http\Api\Schema\Wiki\GroupSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\GroupCollection;
use App\Http\Resources\Wiki\Resource\GroupJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $groups = Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.group.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.group.index'));

    $response->assertJsonStructure([
        GroupCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new GroupSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with($includedPaths->all())->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new GroupSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            GroupJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $groups = Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new GroupSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.group.index', $parameters));

    $groups = $this->sort(Group::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, $query)
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
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $group = Group::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($group, new Query($parameters))
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
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $group = Group::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($group, new Query($parameters))
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

    Group::factory()->count(fake()->randomDigitNotNull())->create();

    Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $group = Group::withoutTrashed()->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($group, new Query($parameters))
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

    Group::factory()->count(fake()->randomDigitNotNull())->create();

    Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $group = Group::withTrashed()->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($group, new Query($parameters))
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

    Group::factory()->count(fake()->randomDigitNotNull())->create();

    Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $group = Group::onlyTrashed()->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($group, new Query($parameters))
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
        Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $groups = Group::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_THEMES,
    ];

    Group::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(Anime::factory())
                ->state(new Sequence(
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with([
        Group::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ])
        ->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_THEMES,
    ];

    Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with([
        Group::RELATION_THEMES => function (HasMany $query) use ($typeFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
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
        IncludeParser::param() => Group::RELATION_ANIME,
    ];

    Group::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    Anime::factory()
                        ->state([
                            Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                        ])
                )
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with([
        Group::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.group.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new GroupCollection($groups, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
