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
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Document\Collection\PageCollection;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\BaseModel;
use App\Models\Document\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $pages = Page::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.page.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($pages, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Page::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.page.index'));

    $response->assertJsonStructure([
        PageCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('sparse fieldsets', function () {
    $schema = new PageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $pages = Page::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($pages, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new PageSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Page::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.page.index', $parameters));

    $pages = $this->sort(Page::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($pages, $query)
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
        Page::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Page::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $page = Page::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
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
        Page::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Page::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $page = Page::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
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

    Page::factory()->count(fake()->randomDigitNotNull())->create();

    Page::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $page = Page::withoutTrashed()->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
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

    Page::factory()->count(fake()->randomDigitNotNull())->create();

    Page::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $page = Page::withTrashed()->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
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

    Page::factory()->count(fake()->randomDigitNotNull())->create();

    Page::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $page = Page::onlyTrashed()->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
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
        Page::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Page::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $page = Page::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = $this->get(route('api.page.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageCollection($page, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
