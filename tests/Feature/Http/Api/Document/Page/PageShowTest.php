<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Resources\Document\Resource\PageJsonResource;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithFaker;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('default', function (): void {
    $page = Page::factory()->create();

    $response = get(route('api.page.show', ['page' => $page]));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageJsonResource($page, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function (): void {
    $page = Page::factory()->trashed()->createOne();

    $page->unsetRelations();

    $response = get(route('api.page.show', ['page' => $page]));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageJsonResource($page, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new PageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PageJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    $page = Page::factory()->create();

    $response = get(route('api.page.show', ['page' => $page] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageJsonResource($page, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
