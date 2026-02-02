<?php

declare(strict_types=1);

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
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
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

test('non null forbidden', function () {
    $nullScopeCount = fake()->randomDigitNotNull();

    $features = Feature::factory()
        ->count($nullScopeCount)
        ->create();

    Collection::times(fake()->randomDigitNotNull(), function () {
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

test('paginated', function () {
    Feature::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.feature.index'));

    $response->assertJsonStructure([
        FeatureCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('sparse fieldsets', function () {
    $schema = new FeatureSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeatureJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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

test('sorts', function () {
    $schema = new FeatureSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
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
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
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
        Feature::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
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
