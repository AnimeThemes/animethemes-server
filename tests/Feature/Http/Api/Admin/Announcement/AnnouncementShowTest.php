<?php

declare(strict_types=1);

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Resources\Admin\Resource\AnnouncementJsonResource;
use App\Models\Admin\Announcement;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('default', function () {
    $announcement = Announcement::factory()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement]));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementJsonResource($announcement, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('cannot view past announcement', function () {
    $announcement = Announcement::factory()->past()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement]));

    $response->assertForbidden();
});

test('cannot view future announcement', function () {
    $announcement = Announcement::factory()->future()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement]));

    $response->assertForbidden();
});

test('sparse fieldsets', function () {
    $schema = new AnnouncementSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            AnnouncementJsonResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
        ],
    ];

    $announcement = Announcement::factory()->create();

    $response = get(route('api.announcement.show', ['announcement' => $announcement] + $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new AnnouncementJsonResource($announcement, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
