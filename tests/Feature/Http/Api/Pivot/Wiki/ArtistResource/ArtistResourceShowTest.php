<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistResourceResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $response = $this->get(route('api.artistresource.show', ['artist' => $artist, 'resource' => $resource]));

    $response->assertNotFound();
});

test('default', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

    $artistResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistResourceResource($artistResource, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ArtistResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $artistResource->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistResourceResource($artistResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $artistResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistResourceResource($artistResource, new Query($parameters))
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
        IncludeParser::param() => ArtistResource::RELATION_RESOURCE,
    ];

    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.artistresource.show', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $artistResource->unsetRelations()->load([
        ArtistResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistResourceResource($artistResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
