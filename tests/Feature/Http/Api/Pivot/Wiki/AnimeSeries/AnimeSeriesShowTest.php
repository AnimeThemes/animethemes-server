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
use App\Http\Api\Schema\Pivot\Wiki\AnimeSeriesSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $series = Series::factory()->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $anime, 'series' => $series]));

    $response->assertNotFound();
});

test('default', function () {
    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

    $animeSeries->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeSeriesSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

    $animeSeries->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeSeriesSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeSeriesJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

    $animeSeries->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query($parameters))
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
        IncludeParser::param() => AnimeSeries::RELATION_ANIME,
    ];

    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

    $animeSeries->unsetRelations()->load([
        AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query($parameters))
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
        IncludeParser::param() => AnimeSeries::RELATION_ANIME,
    ];

    $animeSeries = AnimeSeries::factory()
        ->for(Anime::factory())
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

    $animeSeries->unsetRelations()->load([
        AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function () {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => AnimeSeries::RELATION_ANIME,
    ];

    $animeSeries = AnimeSeries::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->for(Series::factory())
        ->createOne();

    $response = get(route('api.animeseries.show', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series] + $parameters));

    $animeSeries->unsetRelations()->load([
        AnimeSeries::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeSeriesJsonResource($animeSeries, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
