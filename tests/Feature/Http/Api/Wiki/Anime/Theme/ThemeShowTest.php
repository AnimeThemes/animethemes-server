<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\Anime\ThemeSchema;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->unsetRelations();

    $response = get(route('api.animetheme.show', ['animetheme' => $theme]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $theme->unsetRelations();

    $response = get(route('api.animetheme.show', ['animetheme' => $theme]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function () {
    $schema = new ThemeSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->for(Song::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load($includedPaths->all());

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new ThemeSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->count(fake()->randomDigitNotNull())
        ->createOne();

    $theme->unsetRelations();

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter) {
            $query->where(Anime::ATTRIBUTE_MEDIA_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_ANIME,
    ];

    $theme = AnimeTheme::factory()
        ->for(
            Anime::factory()
                ->state([
                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                ])
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_IMAGES,
    ];

    $theme = AnimeTheme::factory()
        ->for(
            Anime::factory()
                ->has(Image::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_IMAGES => function (BelongsToMany $query) use ($facetFilter) {
            $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($nsfwFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(AnimeThemeEntry::factory()->count(fake()->randomDigitNotNull()))
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($spoilerFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by version', function () {
    $versionFilter = fake()->randomDigitNotNull();
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => AnimeTheme::RELATION_ENTRIES,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->state(new Sequence(
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                ))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_ENTRIES => function (HasMany $query) use ($versionFilter) {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($lyricsFilter) {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($ncFilter) {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($overlapFilter) {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(
                    Video::factory()
                        ->count(fake()->randomDigitNotNull())
                        ->state(new Sequence(
                            [Video::ATTRIBUTE_RESOLUTION => $resolutionFilter],
                            [Video::ATTRIBUTE_RESOLUTION => $excludedResolution],
                        ))
                )
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($resolutionFilter) {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($sourceFilter) {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($subbedFilter) {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
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
        IncludeParser::param() => AnimeTheme::RELATION_VIDEOS,
    ];

    $theme = AnimeTheme::factory()
        ->for(Anime::factory())
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        )
        ->createOne();

    $theme->unsetRelations()->load([
        AnimeTheme::RELATION_VIDEOS => function (BelongsToMany $query) use ($uncenFilter) {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ]);

    $response = get(route('api.animetheme.show', ['animetheme' => $theme] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ThemeResource($theme, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
