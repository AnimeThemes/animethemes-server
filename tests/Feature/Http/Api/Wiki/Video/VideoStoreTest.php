<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $video = Video::factory()->makeOne();

    $response = post(route('api.video.store', $video->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $video = Video::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.video.store', $video->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.video.store'));

    $response->assertJsonValidationErrors([
        Video::ATTRIBUTE_BASENAME,
        Video::ATTRIBUTE_FILENAME,
        Video::ATTRIBUTE_MIMETYPE,
        Video::ATTRIBUTE_PATH,
        Video::ATTRIBUTE_SIZE,
    ]);
});

test('create', function () {
    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $parameters = array_merge(
        Video::factory()->raw(),
        [
            Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
            Video::ATTRIBUTE_SOURCE => $source->localize(),
        ]
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.video.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Video::class, 1);
});
