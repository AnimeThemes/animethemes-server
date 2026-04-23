<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Wiki\SynonymSchema;
use App\Http\Resources\Wiki\Resource\SynonymJsonResource;
use App\Models\Wiki\Synonym;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $synonym = Synonym::factory()->forAnime()->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.synonym.show', ['synonym' => $synonym]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymJsonResource($synonym, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $synonym = Synonym::factory()
        ->trashed()
        ->forAnime()
        ->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.synonym.show', ['synonym' => $synonym]));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymJsonResource($synonym, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new SynonymSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            SynonymJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $synonym = Synonym::factory()->forAnime()->createOne();

    $synonym->unsetRelations();

    $response = get(route('api.synonym.show', ['synonym' => $synonym] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new SynonymJsonResource($synonym, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
