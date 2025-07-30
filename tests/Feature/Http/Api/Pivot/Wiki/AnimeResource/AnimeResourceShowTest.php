<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $anime, 'resource' => $resource]));

    $response->assertNotFound();
});

test('default', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

    $animeResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('resources by site', function () {
    $siteFilter = Arr::random(ResourceSite::cases());

    $parameters = [
        FilterParser::param() => [
            ExternalResource::ATTRIBUTE_SITE => $siteFilter->localize(),
        ],
        IncludeParser::param() => AnimeResource::RELATION_RESOURCE,
    ];

    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations()->load([
        AnimeResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
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
        IncludeParser::param() => AnimeResource::RELATION_ANIME,
    ];

    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations()->load([
        AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
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
        IncludeParser::param() => AnimeResource::RELATION_ANIME,
    ];

    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations()->load([
        AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
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
        IncludeParser::param() => AnimeResource::RELATION_ANIME,
    ];

    $animeResource = AnimeResource::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.animeresource.show', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $animeResource->unsetRelations()->load([
        AnimeResource::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeResourceResource($animeResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
