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
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Resource\StudioJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $studio = Studio::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.studio.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Studio::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.studio.index'));

    $response->assertJsonStructure([
        StudioCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new StudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $studio = Studio::with($includedPaths->all())->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new StudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            StudioJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $studio = Studio::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new StudioSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Studio::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.studio.index', $parameters));

    $studios = $this->sort(Studio::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studios, $query)
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
        Studio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Studio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $studio = Studio::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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
        Studio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Studio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $studio = Studio::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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

    Studio::factory()->count(fake()->randomDigitNotNull())->create();

    Studio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $studio = Studio::withoutTrashed()->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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

    Studio::factory()->count(fake()->randomDigitNotNull())->create();

    Studio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $studio = Studio::withTrashed()->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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

    Studio::factory()->count(fake()->randomDigitNotNull())->create();

    Studio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $studio = Studio::onlyTrashed()->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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
        Studio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Studio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $studio = Studio::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $studio = Studio::with([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    Studio::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $studio = Studio::with([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function (): void {
    $yearFilter = fake()->numberBetween(2000, 2002);

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Studio::RELATION_ANIME,
    ];

    Studio::factory()
        ->has(
            Anime::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [Anime::ATTRIBUTE_YEAR => 2000],
                    [Anime::ATTRIBUTE_YEAR => 2001],
                    [Anime::ATTRIBUTE_YEAR => 2002],
                ))
        )
        ->count(fake()->randomDigitNotNull())
        ->create();

    $studio = Studio::with([
        Studio::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studio, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_RESOURCES,
    ];

    Studio::factory()
        ->has(ExternalResource::factory()->count(fake()->randomDigitNotNull()), Studio::RELATION_RESOURCES)
        ->count(fake()->randomDigitNotNull())
        ->create();

    $studios = Studio::with([
        Studio::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter): void {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($studios, new Query($parameters))
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
        IncludeParser::param() => Studio::RELATION_IMAGES,
    ];

    Studio::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $anime = Studio::with([
        Studio::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter): void {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ])
        ->get();

    $response = get(route('api.studio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioCollection($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
