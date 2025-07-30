<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $video = Video::factory()->createOne();

    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $parameters = array_merge(
        Video::factory()->raw(),
        [
            Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
            Video::ATTRIBUTE_SOURCE => $source->localize(),
        ]
    );

    $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $video = Video::factory()->createOne();

    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $parameters = array_merge(
        Video::factory()->raw(),
        [
            Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
            Video::ATTRIBUTE_SOURCE => $source->localize(),
        ]
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $video = Video::factory()->trashed()->createOne();

    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $parameters = array_merge(
        Video::factory()->raw(),
        [
            Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
            Video::ATTRIBUTE_SOURCE => $source->localize(),
        ]
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $video = Video::factory()->createOne();

    $overlap = Arr::random(VideoOverlap::cases());
    $source = Arr::random(VideoSource::cases());

    $parameters = array_merge(
        Video::factory()->raw(),
        [
            Video::ATTRIBUTE_OVERLAP => $overlap->localize(),
            Video::ATTRIBUTE_SOURCE => $source->localize(),
        ]
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.video.update', ['video' => $video] + $parameters));

    $response->assertOk();
});
