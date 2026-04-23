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
use App\Http\Api\Schema\Wiki\Video\ScriptSchema;
use App\Http\Resources\Wiki\Video\Resource\ScriptJsonResource;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $script = VideoScript::factory()->create();

    $response = get(route('api.videoscript.show', ['videoscript' => $script]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $script = VideoScript::factory()->trashed()->createOne();

    $response = get(route('api.videoscript.show', ['videoscript' => $script]));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function (): void {
    $schema = new ScriptSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->createOne();

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ScriptSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ScriptJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $script = VideoScript::factory()->create();

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter): void {
            $query->where(Video::ATTRIBUTE_LYRICS, $lyricsFilter);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter): void {
            $query->where(Video::ATTRIBUTE_NC, $ncFilter);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter): void {
            $query->where(Video::ATTRIBUTE_OVERLAP, $overlapFilter->value);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(
            Video::factory()->state([
                Video::ATTRIBUTE_RESOLUTION => fake()->boolean() ? $resolutionFilter : $excludedResolution,
            ])
        )
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter): void {
            $query->where(Video::ATTRIBUTE_RESOLUTION, $resolutionFilter);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter): void {
            $query->where(Video::ATTRIBUTE_SOURCE, $sourceFilter->value);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter): void {
            $query->where(Video::ATTRIBUTE_SUBBED, $subbedFilter);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
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
        IncludeParser::param() => VideoScript::RELATION_VIDEO,
    ];

    $script = VideoScript::factory()
        ->for(Video::factory())
        ->create();

    $script->unsetRelations()->load([
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter): void {
            $query->where(Video::ATTRIBUTE_UNCEN, $uncenFilter);
        },
    ]);

    $response = get(route('api.videoscript.show', ['videoscript' => $script] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ScriptJsonResource($script, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
