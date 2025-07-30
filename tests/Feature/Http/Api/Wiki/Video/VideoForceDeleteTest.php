<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $video = Video::factory()->createOne();

    $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $video = Video::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

    $response->assertForbidden();
});

test('deleted', function () {
    $video = Video::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.video.forceDelete', ['video' => $video]));

    $response->assertOk();
    static::assertModelMissing($video);
});
