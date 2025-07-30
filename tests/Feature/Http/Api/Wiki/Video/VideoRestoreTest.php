<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $video = Video::factory()->trashed()->createOne();

    $response = $this->patch(route('api.video.restore', ['video' => $video]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $video = Video::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.video.restore', ['video' => $video]));

    $response->assertForbidden();
});

test('trashed', function () {
    $video = Video::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.video.restore', ['video' => $video]));

    $response->assertForbidden();
});

test('restored', function () {
    $video = Video::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.video.restore', ['video' => $video]));

    $response->assertOk();
    static::assertNotSoftDeleted($video);
});
