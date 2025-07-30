<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\SongResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $response = $this->get(route('api.songresource.show', ['song' => $song, 'resource' => $resource]));

    $response->assertNotFound();
});

test('default', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource]));

    $songResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceResource($songResource, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new SongResourceSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $songResource->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceResource($songResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new SongResourceSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SongResourceResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $songResource->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceResource($songResource, new Query($parameters))
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
        IncludeParser::param() => SongResource::RELATION_RESOURCE,
    ];

    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->get(route('api.songresource.show', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $songResource->unsetRelations()->load([
        SongResource::RELATION_RESOURCE => function (BelongsTo $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new SongResourceResource($songResource, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
