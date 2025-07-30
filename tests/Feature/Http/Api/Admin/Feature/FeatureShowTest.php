<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeatureSchema;
use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Models\Admin\Feature;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $feature = Feature::factory()->create();

    $response = get(route('api.feature.show', ['feature' => $feature]));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureResource($feature, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('non null forbidden', function () {
    $feature = Feature::factory()->create([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    $response = get(route('api.feature.show', ['feature' => $feature]));

    $response->assertForbidden();
});

test('sparse fieldsets', function () {
    $schema = new FeatureSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeatureResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $feature = Feature::factory()->create();

    $response = get(route('api.feature.show', ['feature' => $feature] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureResource($feature, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
