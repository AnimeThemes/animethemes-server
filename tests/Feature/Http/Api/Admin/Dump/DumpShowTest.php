<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\DumpSchema;
use App\Http\Resources\Admin\Resource\DumpResource;
use App\Models\Admin\Dump;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $dump = Dump::factory()->create();

    $response = get(route('api.dump.show', ['dump' => $dump]));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpResource($dump, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('cannot view unsafe', function () {
    $dump = Dump::factory()->unsafe()->create();

    $response = get(route('api.dump.show', ['dump' => $dump]));

    $response->assertForbidden();
});

test('sparse fieldsets', function () {
    $schema = new DumpSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            DumpResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $dump = Dump::factory()->create();

    $response = get(route('api.dump.show', ['dump' => $dump] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new DumpResource($dump, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
