<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\ArtistSchema;
use App\Http\Resources\Wiki\Resource\ArtistJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $artist = Artist::factory()->create();

    $response = get(route('api.artist.show', ['artist' => $artist]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $artist->unsetRelations();

    $response = get(route('api.artist.show', ['artist' => $artist]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ArtistSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $artist = Artist::factory()->jsonApiResource()->createOne();

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ArtistSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $artist = Artist::factory()->create();

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by sequence', function () {
    $sequenceFilter = fake()->randomDigitNotNull();
    $excludedSequence = $sequenceFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter,
        ],
        IncludeParser::param() => Artist::RELATION_ANIMETHEMES,
    ];

    $artist = Artist::factory()
        ->has(
            Song::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->count(fake()->randomDigitNotNull())
                        ->state(new Sequence(
                            [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                            [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                        ))
                )
        )
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('themes by type', function () {
    $typeFilter = Arr::random(ThemeType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => Artist::RELATION_ANIMETHEMES,
    ];

    $artist = Artist::factory()
        ->has(
            Song::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->count(fake()->randomDigitNotNull())
                )
        )
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_ANIMETHEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
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
        IncludeParser::param() => Artist::RELATION_ANIME,
    ];

    $artist = Artist::factory()
        ->has(
            Song::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->count(fake()->randomDigitNotNull())
                )
        )
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
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
        IncludeParser::param() => Artist::RELATION_ANIME,
    ];

    $artist = Artist::factory()
        ->has(
            Song::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->count(fake()->randomDigitNotNull())
                )
        )
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
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
        IncludeParser::param() => Artist::RELATION_ANIME,
    ];

    $artist = Artist::factory()
        ->has(
            Song::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    AnimeTheme::factory()
                        ->for(
                            Anime::factory()
                                ->state([
                                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                                ])
                        )
                        ->count(fake()->randomDigitNotNull())
                )
        )
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
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
        IncludeParser::param() => Artist::RELATION_RESOURCES,
    ];

    $artist = Artist::factory()
        ->has(ExternalResource::factory()->count(fake()->randomDigitNotNull()), Artist::RELATION_RESOURCES)
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
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
        IncludeParser::param() => Artist::RELATION_IMAGES,
    ];

    $artist = Artist::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $artist->unsetRelations()->load([
        Artist::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response = get(route('api.artist.show', ['artist' => $artist] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistJsonResource($artist, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
