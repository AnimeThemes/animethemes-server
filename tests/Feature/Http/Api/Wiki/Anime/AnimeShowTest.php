<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\ThemeType;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Resources\Wiki\Anime\Resource\AnimeSynonymJsonResource;
use App\Http\Resources\Wiki\Anime\Resource\ThemeJsonResource;
use App\Http\Resources\Wiki\Resource\AnimeJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $anime = Anime::factory()->create();

    $response = get(route('api.anime.show', ['anime' => $anime]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $anime->unsetRelations();

    $response = get(route('api.anime.show', ['anime' => $anime]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new AnimeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new AnimeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $anime = Anime::factory()->create();

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('synonyms by type', function () {
    $typeFilter = Arr::random(AnimeSynonymType::cases());

    $parameters = [
        FilterParser::param() => [
            AnimeSynonymJsonResource::$wrap => [
                AnimeSynonym::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_ANIMESYNONYMS,
    ];

    $anime = Anime::factory()
        ->has(AnimeSynonym::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_ANIMESYNONYMS => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeSynonym::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_THEMES,
    ];

    $anime = Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $sequenceFilter],
                    [AnimeTheme::ATTRIBUTE_SEQUENCE => $excludedSequence],
                ))
        )
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
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
            ThemeJsonResource::$wrap => [
                AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_THEMES,
    ];

    $anime = Anime::factory()
        ->has(AnimeTheme::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter) {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by nsfw', function () {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    $anime = Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->has(AnimeThemeEntry::factory()->count(fake()->numberBetween(1, 3)))
                ->count(fake()->numberBetween(1, 3))
        )
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by spoiler', function () {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    $anime = Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->has(AnimeThemeEntry::factory()->count(fake()->numberBetween(1, 3)))
                ->count(fake()->numberBetween(1, 3))
        )
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by version', function () {
    $versionFilter = fake()->numberBetween(1, 3);
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => Anime::RELATION_ENTRIES,
    ];

    $anime = Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->numberBetween(1, 3))
                ->has(
                    AnimeThemeEntry::factory()
                        ->count(fake()->numberBetween(1, 3))
                        ->state(new Sequence(
                            [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                            [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                        ))
                )
        )
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_RESOURCES,
    ];

    $anime = Anime::factory()
        ->has(ExternalResource::factory()->count(fake()->randomDigitNotNull()), Anime::RELATION_RESOURCES)
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter) {
            $query->where(ExternalResource::ATTRIBUTE_SITE, $siteFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
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
        IncludeParser::param() => Anime::RELATION_IMAGES,
    ];

    $anime = Anime::factory()
        ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by lyrics', function () {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by nc', function () {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by overlap', function () {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by resolution', function () {
    $resolutionFilter = fake()->randomNumber();
    $excludedResolution = $resolutionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()
        ->has(
            AnimeTheme::factory()
                ->count(fake()->numberBetween(1, 3))
                ->has(
                    AnimeThemeEntry::factory()
                        ->count(fake()->numberBetween(1, 3))
                        ->has(
                            Video::factory()
                                ->count(fake()->numberBetween(1, 3))
                                ->state(new Sequence(
                                    [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                                    [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                                ))
                        )
                )
        )
        ->create();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by source', function () {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by subbed', function () {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('videos by uncen', function () {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ]);

    $response = get(route('api.anime.show', ['anime' => $anime] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeJsonResource($anime, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
