<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeThemeEntryVideoSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('not found', function (): void {
    $entry = AnimeThemeEntry::factory()
        ->for(AnimeTheme::factory()->for(Anime::factory()))
        ->create();

    $video = Video::factory()->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entry, 'video' => $video]));

    $response->assertNotFound();
});

test('default', function (): void {
    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video]));

    $entryVideo->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function (): void {
    $schema = new AnimeThemeEntryVideoSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load($includedPaths->all());

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new AnimeThemeEntryVideoSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnimeThemeEntryVideoJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations();

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entry by nsfw', function (): void {
    $nsfwFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_NSFW => $nsfwFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($nsfwFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_NSFW, $nsfwFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entry by spoiler', function (): void {
    $spoilerFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_SPOILER => $spoilerFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($spoilerFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_SPOILER, $spoilerFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('entry by version', function (): void {
    $versionFilter = fake()->randomDigitNotNull();

    $parameters = [
        FilterParser::param() => [
            AnimeThemeEntry::ATTRIBUTE_VERSION => $versionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_ENTRY,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_ENTRY => function (BelongsTo $query) use ($versionFilter): void {
            $query->where(AnimeThemeEntry::ATTRIBUTE_VERSION, $versionFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by lyrics', function (): void {
    $lyricsFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_LYRICS => $lyricsFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter): void {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by nc', function (): void {
    $ncFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_NC => $ncFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter): void {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by overlap', function (): void {
    $overlapFilter = Arr::random(VideoOverlap::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_OVERLAP => $overlapFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter): void {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by resolution', function (): void {
    $resolutionFilter = fake()->randomNumber();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_RESOLUTION => $resolutionFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter): void {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by source', function (): void {
    $sourceFilter = Arr::random(VideoSource::cases());

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SOURCE => $sourceFilter->localize(),
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter): void {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by subbed', function (): void {
    $subbedFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_SUBBED => $subbedFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter): void {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('video by uncen', function (): void {
    $uncenFilter = fake()->boolean();

    $parameters = [
        FilterParser::param() => [
            Video::ATTRIBUTE_UNCEN => $uncenFilter,
        ],
        IncludeParser::param() => AnimeThemeEntryVideo::RELATION_VIDEO,
    ];

    $entryVideo = AnimeThemeEntryVideo::factory()
        ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.animethemeentryvideo.show', ['animethemeentry' => $entryVideo->animethemeentry, 'video' => $entryVideo->video] + $parameters));

    $entryVideo->unsetRelations()->load([
        AnimeThemeEntryVideo::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter): void {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ]);

    $response->assertJson(
        json_decode(
            json_encode(
                new AnimeThemeEntryVideoJsonResource($entryVideo, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
