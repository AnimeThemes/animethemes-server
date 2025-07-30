<?php

declare(strict_types=1);

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\ResourceSite;
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
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\SongResourceCollection;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $songResources = SongResource::all();

    $response = get(route('api.songresource.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $response = get(route('api.songresource.index'));

    $response->assertJsonStructure([
        SongResourceCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new SongResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $response = get(route('api.songresource.index', $parameters));

    $songResources = SongResource::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SongResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SongResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $response = get(route('api.songresource.index', $parameters));

    $songResources = SongResource::all();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new SongResourceSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $response = get(route('api.songresource.index', $parameters));

    $songResources = $this->sort(SongResource::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, $query)
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
            BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($createdFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });
    });

    $songResources = SongResource::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.songresource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query($parameters))
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
            BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Carbon::withTestNow($updatedFilter, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });
    });

    Carbon::withTestNow($excludedDate, function () {
        Collection::times(fake()->randomDigitNotNull(), function () {
            SongResource::factory()
                ->for(Song::factory())
                ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
                ->create();
        });
    });

    $songResources = SongResource::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.songresource.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query($parameters))
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
        IncludeParser::param() => SongResource::RELATION_RESOURCE,
    ];

    Collection::times(fake()->randomDigitNotNull(), function () {
        SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
            ->create();
    });

    $response = get(route('api.songresource.index', $parameters));

    $songResources = SongResource::with([
        SongResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ])
        ->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceCollection($songResources, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
