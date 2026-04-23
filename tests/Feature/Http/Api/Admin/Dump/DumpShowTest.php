<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Resources\Admin\Resource\DumpJsonResource;
use App\Models\Admin\Dump;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $dump = Dump::factory()->create();

    $response = get(route('api.dump.show', ['dump' => $dump]));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpJsonResource($dump, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('cannot view private', function (): void {
    $dump = Dump::factory()->private()->create();

    $response = get(route('api.dump.show', ['dump' => $dump]));

    $response->assertForbidden();
});

test('sparse fieldsets', function (): void {
    $schema = new DumpSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            DumpJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $dump = Dump::factory()->create();

    $response = get(route('api.dump.show', ['dump' => $dump] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpJsonResource($dump, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
