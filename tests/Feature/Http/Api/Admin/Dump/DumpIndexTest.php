<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\DumpCollection;
use App\Http\Resources\Admin\Resource\DumpJsonResource;
use App\Models\Admin\Dump;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $dumps = Dump::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.dump.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dumps, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('private', function (): void {
    Dump::factory()
        ->private()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $dumps = Dump::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.dump.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dumps, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Dump::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.dump.index'));

    $response->assertJsonStructure([
        DumpCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('sparse fieldsets', function (): void {
    $schema = new DumpSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            DumpJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $dumps = Dump::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.dump.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dumps, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new DumpSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Dump::factory()->count(fake()->randomDigitNotNull())->create();

    $response = get(route('api.dump.index', $parameters));

    $dumps = $this->sort(Dump::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dumps, $query)
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
        Dump::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Dump::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $dump = Dump::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.dump.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dump, new Query($parameters))
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
        Dump::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Dump::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $dump = Dump::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.dump.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpCollection($dump, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
