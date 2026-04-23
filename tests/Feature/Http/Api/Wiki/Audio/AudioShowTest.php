<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Resources\Wiki\Resource\AudioJsonResource;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $audio = Audio::factory()->create();

    $response = get(route('api.audio.show', ['audio' => $audio]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioJsonResource($audio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $audio = Audio::factory()->trashed()->createOne();

    $response = get(route('api.audio.show', ['audio' => $audio]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioJsonResource($audio, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('allowed include paths', function (): void {
    $schema = new AudioSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    $audio = Audio::factory()
        ->has(Video::factory()->count(fake()->randomDigitNotNull()))
        ->create();

    $response = get(route('api.audio.show', ['audio' => $audio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioJsonResource($audio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new AudioSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AudioJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $audio = Audio::factory()->create();

    $response = get(route('api.audio.show', ['audio' => $audio] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AudioJsonResource($audio, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
