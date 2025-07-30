<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $image = Image::factory()->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $anime, 'image' => $image]));

    $response->assertNotFound();
});

test('default', function () {
    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image]));

    $animeImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
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
        IncludeParser::param() => AnimeImage::RELATION_IMAGE,
    ];

    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations()->load([
        AnimeImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
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
        IncludeParser::param() => AnimeImage::RELATION_ANIME,
    ];

    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations()->load([
        AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
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
        IncludeParser::param() => AnimeImage::RELATION_ANIME,
    ];

    $animeImage = AnimeImage::factory()
        ->for(Anime::factory())
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations()->load([
        AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
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
        IncludeParser::param() => AnimeImage::RELATION_ANIME,
    ];

    $animeImage = AnimeImage::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->for(Image::factory())
        ->createOne();

    $response = $this->get(route('api.animeimage.show', ['anime' => $animeImage->anime, 'image' => $animeImage->image] + $parameters));

    $animeImage->unsetRelations()->load([
        AnimeImage::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeImageResource($animeImage, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
