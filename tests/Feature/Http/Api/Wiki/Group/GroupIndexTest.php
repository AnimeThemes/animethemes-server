<?php

declare(strict_types=1);

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
use App\Http\Resources\Wiki\Resource\GroupResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $groups = Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.group.index'));

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

test('paginated', function () {
    Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.group.index'));

    $response->assertJsonStructure([
        GroupCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new GroupSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Group::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull())->for(Anime::factory()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $groups = Group::with($includedPaths->all())->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('sparse fieldsets', function () {
    $schema = new GroupSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            GroupResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $groups = Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.group.index', $parameters));

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

test('sorts', function () {
    $schema = new GroupSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Group::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.group.index', $parameters));

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
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $group = Group::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.group.index', $parameters));

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
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Group::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $group = Group::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('without trashed filter', function () {
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

    $response = $this->get(route('api.group.index', $parameters));

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

test('with trashed filter', function () {
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

    $response = $this->get(route('api.group.index', $parameters));

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

test('only trashed filter', function () {
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

    $response = $this->get(route('api.group.index', $parameters));

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
        Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Group::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $groups = Group::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('themes by sequence', function () {
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
        Group::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('themes by type', function () {
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
        Group::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('anime by media format', function () {
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
        Group::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('anime by season', function () {
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
        Group::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.group.index', $parameters));

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

test('anime by year', function () {
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
        Group::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.group.index', $parameters));

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
