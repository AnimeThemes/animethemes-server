<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\StudioResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $studio = Studio::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $response = get(route('api.studioresource.show', ['studio' => $studio, 'resource' => $resource]));

    $response->assertNotFound();
});

test('default', function () {
    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource]));

    $studioResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioResourceResource($studioResource, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new StudioResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $studioResource->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioResourceResource($studioResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new StudioResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            StudioResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $studioResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioResourceResource($studioResource, new Query($parameters))
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
        IncludeParser::param() => StudioResource::RELATION_RESOURCE,
    ];

    $studioResource = StudioResource::factory()
        ->for(Studio::factory())
        ->for(ExternalResource::factory(), StudioResource::RELATION_RESOURCE)
        ->createOne();

    $response = get(route('api.studioresource.show', ['studio' => $studioResource->studio, 'resource' => $studioResource->resource] + $parameters));

    $studioResource->unsetRelations()->load([
        StudioResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new StudioResourceResource($studioResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
