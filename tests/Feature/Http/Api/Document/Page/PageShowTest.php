<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\Document\Page;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $page = Page::factory()->create();

    $response = $this->get(route('api.page.show', ['page' => $page]));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageResource($page, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('soft delete', function () {
    $page = Page::factory()->trashed()->createOne();

    $page->unsetRelations();

    $response = $this->get(route('api.page.show', ['page' => $page]));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageResource($page, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function () {
    $schema = new PageSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            PageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $page = Page::factory()->create();

    $response = $this->get(route('api.page.show', ['page' => $page] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new PageResource($page, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
