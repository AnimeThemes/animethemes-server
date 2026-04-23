<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\AggregatesFields;
use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\ExternalProfileVisibility;
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
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Http\Resources\List\Resource\ExternalProfileJsonResource;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(AggregatesFields::class);

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $publicCount = fake()->randomDigitNotNull();

    $profiles = ExternalProfile::factory()
        ->count($publicCount)
        ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

    $privateCount = fake()->randomDigitNotNull();

    ExternalProfile::factory()
        ->count($privateCount)
        ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PRIVATE->value]);

    $response = get(route('api.externalprofile.index'));

    $response->assertJsonCount($publicCount, ExternalProfileCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    ExternalProfile::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

    $response = get(route('api.externalprofile.index'));

    $response->assertJsonStructure([
        ExternalProfileCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new ExternalProfileSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    ExternalProfile::factory()
        ->for(User::factory())
        ->has(ExternalEntry::factory(), ExternalProfile::RELATION_EXTERNAL_ENTRIES)
        ->count(fake()->randomDigitNotNull())
        ->create([
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value,
        ]);

    $profiles = ExternalProfile::with($includedPaths->all())->get();

    $response = get(route('api.externalprofile.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ExternalProfileSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ExternalProfileJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $profiles = ExternalProfile::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

    $response = get(route('api.externalprofile.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new ExternalProfileSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    ExternalProfile::factory()
        ->count(fake()->randomDigitNotNull())
        ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);

    $response = get(route('api.externalprofile.index', $parameters));

    $builder = ExternalProfile::query();
    $this->withAggregates($builder, $query, $schema);
    $profiles = $this->sort($builder, $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, $query)
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
        ExternalProfile::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
    });

    Date::withTestNow($excludedDate, function (): void {
        ExternalProfile::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
    });

    $profiles = ExternalProfile::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.externalprofile.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query($parameters))
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
        ExternalProfile::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
    });

    Date::withTestNow($excludedDate, function (): void {
        ExternalProfile::factory()
            ->count(fake()->randomDigitNotNull())
            ->create([ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::PUBLIC->value]);
    });

    $profiles = ExternalProfile::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.externalprofile.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
