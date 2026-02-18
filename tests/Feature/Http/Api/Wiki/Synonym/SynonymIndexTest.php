<?php

declare(strict_types=1);

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
use App\Http\Api\Schema\Wiki\SynonymSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\BaseModel;
use App\Models\Wiki\Synonym;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

use function Pest\Laravel\get;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonyms = Synonym::all();

    $response = get(route('api.synonym.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonyms, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.synonym.index'));

    $response->assertJsonStructure([
        SynonymCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new SynonymSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonyms = Synonym::with($includedPaths->all())->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonyms, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SynonymSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SynonymJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonyms = Synonym::all();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonyms, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new SynonymSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $response = get(route('api.synonym.index', $parameters));

    $synonyms = $this->sort(Synonym::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonyms, $query)
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
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $synonym = Synonym::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
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
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $synonym = Synonym::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
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

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    Synonym::factory()
        ->trashed()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonym = Synonym::withoutTrashed()->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
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

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    Synonym::factory()
        ->trashed()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonym = Synonym::withTrashed()->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
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

    Synonym::factory()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    Synonym::factory()
        ->trashed()
        ->forAnime()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $synonym = Synonym::onlyTrashed()->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
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
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Synonym::factory()
            ->forAnime()
            ->count(fake()->randomDigitNotNull())
            ->create();
    });

    $synonym = Synonym::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = get(route('api.synonym.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymCollection($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
