<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Enums\Models\Wiki\SynonymType;
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
use App\Http\Resources\Wiki\Anime\Resource\ThemeJsonResource;
use App\Http\Resources\Wiki\Resource\AnimeJsonResource;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Synonym;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
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

test('soft delete', function (): void {
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

test('allowed include paths', function (): void {
    $schema = new AnimeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

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

test('sparse fieldsets', function (): void {
    $schema = new AnimeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
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

test('synonyms by type', function (): void {
    $typeFilter = Arr::random(SynonymType::cases());

    $parameters = [
        FilterParser::param() => [
            SynonymJsonResource::$wrap => [
                Synonym::ATTRIBUTE_TYPE => $typeFilter->localize(),
            ],
        ],
        IncludeParser::param() => Anime::RELATION_ANIMESYNONYMS,
    ];

    $anime = Anime::factory()
        ->has(Synonym::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_ANIMESYNONYMS => function (HasMany $query) use ($typeFilter): void {
            $query->where(Synonym::ATTRIBUTE_TYPE, $typeFilter->value);
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

test('themes by sequence', function (): void {
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
        Anime::RELATION_THEMES => function (HasMany $query) use ($sequenceFilter): void {
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

test('themes by type', function (): void {
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
        Anime::RELATION_THEMES => function (HasMany $query) use ($typeFilter): void {
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

test('entries by nsfw', function (): void {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter): void {
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

test('entries by spoiler', function (): void {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter): void {
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

test('entries by version', function (): void {
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
        Anime::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter): void {
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

test('resources by site', function (): void {
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
        Anime::RELATION_RESOURCES => function (BelongsToMany $query) use ($siteFilter): void {
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

test('images by facet', function (): void {
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
        Anime::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter): void {
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

test('videos by lyrics', function (): void {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter): void {
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

test('videos by nc', function (): void {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter): void {
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

test('videos by overlap', function (): void {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter): void {
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

test('videos by resolution', function (): void {
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
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter): void {
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

test('videos by source', function (): void {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter): void {
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

test('videos by subbed', function (): void {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter): void {
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

test('videos by uncen', function (): void {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => Anime::RELATION_VIDEOS,
    ];

    $anime = Anime::factory()->jsonApiResource()->createOne();

    $anime->unsetRelations()->load([
        Anime::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter): void {
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
