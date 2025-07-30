<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistImageResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\ArtistImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = $this->get(route('api.artistimage.show', ['artist' => $artist, 'image' => $image]));

    $response->assertNotFound();
});

test('default', function () {
    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image]));

    $artistImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageResource($artistImage, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ArtistImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

    $artistImage->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageResource($artistImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

    $artistImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageResource($artistImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('images by facet', function () {
    $facetFilter = Arr::random(ImageFacet::cases());

    $parameters = [
        FilterParser::param() => [
            Image::ATTRIBUTE_FACET => $facetFilter->localize(),
        ],
        IncludeParser::param() => ArtistImage::RELATION_IMAGE,
    ];

    $artistImage = ArtistImage::factory()
        ->for(Artist::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.artistimage.show', ['artist' => $artistImage->artist, 'image' => $artistImage->image] + $parameters));

    $artistImage->unsetRelations()->load([
        ArtistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistImageResource($artistImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
