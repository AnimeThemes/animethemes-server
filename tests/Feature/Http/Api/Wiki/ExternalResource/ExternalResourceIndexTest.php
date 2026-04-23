<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
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
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $resources = ExternalResource::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.resource.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.resource.index'));

    $response->assertJsonStructure([
        ExternalResourceCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new ExternalResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    ExternalResource::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->has(Artist::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $resources = ExternalResource::with($includedPaths->all())->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ExternalResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ExternalResourceJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $resources = ExternalResource::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new ExternalResourceSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.resource.index', $parameters));

    $resources = $this->sort(ExternalResource::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, $query)
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
        ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $resource = ExternalResource::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
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
        ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $resource = ExternalResource::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
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

    ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();

    ExternalResource::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $resource = ExternalResource::withoutTrashed()->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
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

    ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();

    ExternalResource::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $resource = ExternalResource::withTrashed()->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
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

    ExternalResource::factory()->count(fake()->randomDigitNotNull())->create();

    ExternalResource::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $resource = ExternalResource::onlyTrashed()->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
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
        ExternalResource::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        ExternalResource::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $resource = ExternalResource::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('site filter', function (): void {
    $siteFilter = Arr::random(ResourceSite::cases());

    $parameters = [
        FilterParser::param() => [
            ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
        ],
    ];

    ExternalResource::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $resources = ExternalResource::query()->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value)->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
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
        IncludeParser::param() => ExternalResource::RELATION_ANIME,
    ];

    ExternalResource::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $resources = ExternalResource::with([
        ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
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
        IncludeParser::param() => ExternalResource::RELATION_ANIME,
    ];

    ExternalResource::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $resources = ExternalResource::with([
        ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
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
        IncludeParser::param() => ExternalResource::RELATION_ANIME,
    ];

    ExternalResource::factory()
        ->has(
            Anime::factory()
                ->count(fake()->randomDigitNotNull())
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $resources = ExternalResource::with([
        ExternalResource::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.resource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalResourceCollection($resources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
