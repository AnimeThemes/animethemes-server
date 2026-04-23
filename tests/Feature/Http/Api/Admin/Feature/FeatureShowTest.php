<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeatureSchema;
use App\Http\Resources\Admin\Resource\FeatureJsonResource;
use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $feature = Feature::factory()->create();

    $response = get(route('api.feature.show', ['feature' => $feature]));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureJsonResource($feature, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('non null forbidden', function (): void {
    $feature = Feature::factory()->create([
        Feature::ATTRIBUTE_SCOPE => fake()->word(),
    ]);

    $response = get(route('api.feature.show', ['feature' => $feature]));

    $response->assertForbidden();
});

test('sparse fieldsets', function (): void {
    $schema = new FeatureSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            FeatureJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $feature = Feature::factory()->create();

    $response = get(route('api.feature.show', ['feature' => $feature] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new FeatureJsonResource($feature, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
