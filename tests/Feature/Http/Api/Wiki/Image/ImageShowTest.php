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
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Wiki\Resource\ImageResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $image = Image::factory()->create();

    $response = $this->get(route('api.image.show', ['image' => $image]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $image = Image::factory()->trashed()->createOne();

    $image->unsetRelations();

    $response = $this->get(route('api.image.show', ['image' => $image]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ImageSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $image = Image::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->has(Artist::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ImageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $image = Image::factory()->create();

    $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query($parameters))
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
        IncludeParser::param() => Image::RELATION_ANIME,
    ];

    $image = Image::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $image->unsetRelations()->load([
        Image::RELATION_ANIME => function (BelongsToMany $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query($parameters))
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
        IncludeParser::param() => Image::RELATION_ANIME,
    ];

    $image = Image::factory()
        ->has(Anime::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $image->unsetRelations()->load([
        Image::RELATION_ANIME => function (BelongsToMany $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query($parameters))
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
        IncludeParser::param() => Image::RELATION_ANIME,
    ];

    $image = Image::factory()
        ->has(
            Anime::factory()
                ->count(fake()->randomDigitNotNull())
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->createOne();

    $image->unsetRelations()->load([
        Image::RELATION_ANIME => function (BelongsToMany $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = $this->get(route('api.image.show', ['image' => $image] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ImageResource($image, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
