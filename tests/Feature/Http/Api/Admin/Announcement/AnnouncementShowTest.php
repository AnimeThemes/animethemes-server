<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $announcement = Announcement::factory()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementResource($announcement, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('cannot view private', function () {
    $announcement = Announcement::factory()->private()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement]));

    $response->assertForbidden();
});

test('sparse fieldsets', function () {
    $schema = new AnnouncementSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnnouncementResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $announcement = Announcement::factory()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementResource($announcement, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
