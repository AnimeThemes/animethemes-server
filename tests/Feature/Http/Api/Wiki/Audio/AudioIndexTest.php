<?php

declare(strict_types=1);

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
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
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\AudioCollection;
use App\Http\Resources\Wiki\Resource\AudioJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    $audios = Audio::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.audio.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audios, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Audio::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.audio.index'));

    $response->assertJsonStructure([
        AudioCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new AudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Audio::factory()
        ->count(fake()->randomDigitNotNull())
        ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        ->create();

    $audios = Audio::with($includedPaths->all())->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audios, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new AudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AudioJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $audios = Audio::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audios, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new AudioSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Audio::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.audio.index', $parameters));

    $audios = $this->sort(Audio::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audios, $query)
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
        Audio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Audio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $audio = Audio::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
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
        Audio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Audio::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $audio = Audio::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
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

    Audio::factory()->count(fake()->randomDigitNotNull())->create();

    Audio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $audio = Audio::withoutTrashed()->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
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

    Audio::factory()->count(fake()->randomDigitNotNull())->create();

    Audio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $audio = Audio::withTrashed()->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
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

    Audio::factory()->count(fake()->randomDigitNotNull())->create();

    Audio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $audio = Audio::onlyTrashed()->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
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
        Audio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Date::withTestNow($excludedDate, function (): void {
        Audio::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $audio = Audio::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.audio.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioCollection($audio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
