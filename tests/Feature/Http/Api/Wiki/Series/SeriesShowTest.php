<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\Wiki\Resource\SeriesJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $series = Series::factory()->create();

    $response = get(route('api.series.show', ['series' => $series]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $series = Series::factory()->trashed()->createOne();

    $series->unsetRelations();

    $response = get(route('api.series.show', ['series' => $series]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new SeriesSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $series = Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = get(route('api.series.show', ['series' => $series] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query($parameters))
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
            SeriesJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $series = Series::factory()->create();

    $response = get(route('api.series.show', ['series' => $series] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query($parameters))
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

    $series = Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $series->unsetRelations()->load([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.series.show', ['series' => $series] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query($parameters))
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

    $series = Series::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $series->unsetRelations()->load([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.series.show', ['series' => $series] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query($parameters))
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

    $series = Series::factory()
        ->has(
            Anime::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [Anime::ATTRIBUTE_YEAR => 2000],
                    [Anime::ATTRIBUTE_YEAR => 2001],
                    [Anime::ATTRIBUTE_YEAR => 2002],
                ))
        )
        ->createOne();

    $series->unsetRelations()->load([
        Series::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.series.show', ['series' => $series] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SeriesJsonResource($series, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
