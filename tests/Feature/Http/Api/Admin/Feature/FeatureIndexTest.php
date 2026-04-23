<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeatureSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\FeatureCollection;
use App\Http\Resources\Admin\Resource\FeatureJsonResource;
use App\Models\Admin\Feature;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $features = Feature::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.feature.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($features, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('non null forbidden', function (): void {
    $nullScopeCount = fake()->randomDigitNotNull();

    $features = Feature::factory()
        ->count($nullScopeCount)
        ->create();

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        Feature::factory()->create([
            Feature::ATTRIBUTE_SCOPE => fake()->word(),
        ]);
    });

    $response = get(route('api.feature.index'));

    $response->assertJsonCount($nullScopeCount, FeatureCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($features, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Feature::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.feature.index'));

    $response->assertJsonStructure([
        FeatureCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('sparse fieldsets', function (): void {
    $schema = new FeatureSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeatureJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
        SortParser::param() => new IdField($schema, Feature::ATTRIBUTE_ID)->getSort()->format(Direction::ASCENDING),
    ];

    $features = Feature::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.feature.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($features, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new FeatureSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Feature::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.feature.index', $parameters));

    $features = $this->sort(Feature::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($features, $query)
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
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $feature = Feature::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.feature.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($feature, new Query($parameters))
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
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $feature = Feature::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.feature.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureCollection($feature, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
