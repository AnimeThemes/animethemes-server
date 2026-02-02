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
use Illuminate\Support\Arr;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
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

test('soft delete', function () {
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

test('allowed include paths', function () {
    $schema = new ScriptSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

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

test('sparse fieldsets', function () {
    $schema = new ScriptSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ScriptJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
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

test('videos by lyrics', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($lyricsFilter) {
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

test('videos by nc', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($ncFilter) {
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

test('videos by overlap', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($overlapFilter) {
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

test('videos by resolution', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($resolutionFilter) {
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

test('videos by source', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($sourceFilter) {
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

test('videos by subbed', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($subbedFilter) {
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

test('videos by uncen', function () {
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
        VideoScript::RELATION_VIDEO => function (BelongsTo $query) use ($uncenFilter) {
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
