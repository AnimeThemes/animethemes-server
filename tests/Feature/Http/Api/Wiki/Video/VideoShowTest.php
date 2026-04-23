<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Enums\Models\Wiki\ThemeType;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Wiki\Resource\VideoJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $video = Video::factory()->create();

    $response = get(route('api.video.show', ['video' => $video]));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $video = Video::factory()->trashed()->createOne();

    $response = get(route('api.video.show', ['video' => $video]));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function (): void {
    $schema = new VideoSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $video = Video::factory()
        ->for(Audio::factory())
        ->has(VideoScript::factory(), Video::RELATION_SCRIPT)
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new VideoSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            VideoJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $video = Video::factory()->create();

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
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
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($nsfwFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
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
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($spoilerFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entries by version', function (): void {
    $versionFilter = fake()->randomDigitNotNull();
    $excludedVersion = $versionFilter + 1;

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEMEENTRIES,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
                ->state(new Sequence(
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter],
                    [AnimeThemeEntry::ATTRIBUTE_VERSION => $excludedVersion],
                ))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIMETHEMEENTRIES => function (BelongsToMany $query) use ($versionFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
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
        IncludeParser::param() => Video::RELATION_ANIMETHEME,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    AnimeTheme::factory()
                        ->for(Anime::factory())
                        ->state([
                            AnimeTheme::ATTRIBUTE_SEQUENCE => fake()->boolean() ? $sequenceFilter : $excludedSequence,
                        ])
                )
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($sequenceFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_SEQUENCE, $sequenceFilter);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
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
            AnimeTheme::ATTRIBUTE_TYPE => $typeFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIMETHEME,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIMETHEME => function (BelongsTo $query) use ($typeFilter): void {
            $query->where(AnimeTheme::ATTRIBUTE_TYPE, $typeFilter->value);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by media format', function (): void {
    $mediaFormatFilter = Arr::random(AnimeMediaFormat::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormatFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIME => function (BelongsTo $query) use ($mediaFormatFilter): void {
            $query->where(Anime::ATTRIBUTE_FORMAT, $mediaFormatFilter->value);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by season', function (): void {
    $seasonFilter = Arr::random(AnimeSeason::cases());

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_SEASON => $seasonFilter->localize(),
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(AnimeTheme::factory()->for(Anime::factory()))
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter): void {
            $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('anime by year', function (): void {
    $yearFilter = intval(fake()->year());
    $excludedYear = $yearFilter + 1;

    $parameters = [
        FilterParser::param() => [
            Anime::ATTRIBUTE_YEAR => $yearFilter,
        ],
        IncludeParser::param() => Video::RELATION_ANIME,
    ];

    $video = Video::factory()
        ->has(
            AnimeThemeEntry::factory()
                ->count(fake()->randomDigitNotNull())
                ->for(
                    AnimeTheme::factory()
                        ->for(
                            Anime::factory()
                                ->state([
                                    Anime::ATTRIBUTE_YEAR => fake()->boolean() ? $yearFilter : $excludedYear,
                                ])
                        )
                )
        )
        ->createOne();

    $video->unsetRelations()->load([
        Video::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter): void {
            $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
        },
    ]);

    $response = get(route('api.video.show', ['video' => $video] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new VideoJsonResource($video, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
