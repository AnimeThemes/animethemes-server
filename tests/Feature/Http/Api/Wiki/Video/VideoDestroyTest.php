<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $video = Video::factory()->createOne();

    $response = delete(route('api.video.destroy', ['video' => $video]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $video = Video::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.video.destroy', ['video' => $video]));

    $response->assertForbidden();
});

test('trashed', function () {
    $video = Video::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.video.destroy', ['video' => $video]));

    $response->assertNotFound();
});

test('deleted', function () {
    $video = Video::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Video::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.video.destroy', ['video' => $video]));

    $response->assertOk();
    $this->assertSoftDeleted($video);
});
