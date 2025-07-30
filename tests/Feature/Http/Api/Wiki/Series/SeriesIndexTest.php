<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
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
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

uses(App\Concerns\Actions\Http\Api\SortsModels::class);

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $series = Series::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.series.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function () {
    Series::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.series.index'));

    $response->assertJsonStructure([
        SeriesCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function () {
    $schema = new SeriesSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $series = Series::with($includedPaths->all())->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SeriesSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SeriesResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $series = Series::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function () {
    $schema = new SeriesSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field) => $field instanceof SortableField)
        ->map(fn (SortableField $field) => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Series::factory()->count(fake()->randomDigitNotNull())->create();

    $response = $this->get(route('api.series.index', $parameters));

    $series = $this->sort(Series::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, $query)
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
        Series::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Series::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $series = Series::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
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
        Series::factory()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Series::factory()->count(fake()->randomDigitNotNull())->create();
    });

    $series = Series::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
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

    Series::factory()->count(fake()->randomDigitNotNull())->create();

    Series::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $series = Series::withoutTrashed()->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
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

    Series::factory()->count(fake()->randomDigitNotNull())->create();

    Series::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $series = Series::withTrashed()->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
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

    Series::factory()->count(fake()->randomDigitNotNull())->create();

    Series::factory()->trashed()->count(fake()->randomDigitNotNull())->create();

    $series = Series::onlyTrashed()->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
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
        Series::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    Carbon::withTestNow($excludedDate, function () {
        Series::factory()->trashed()->count(fake()->randomDigitNotNull())->create();
    });

    $series = Series::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function () {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Series::RELATION_ANIME,
    ];

    Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $series = Series::with([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function () {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Series::RELATION_ANIME,
    ];

    Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->count(fake()->randomDigitNotNull())
        ->create();

    $series = Series::with([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ])
        ->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function () {
    $yearFilter = fake()->numberBetween(2000, 2002);

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Series::RELATION_ANIME,
    ];

    Series::factory()
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

    $series = Series::with([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ])
        ->get();

    $response = $this->get(route('api.series.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesCollection($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
