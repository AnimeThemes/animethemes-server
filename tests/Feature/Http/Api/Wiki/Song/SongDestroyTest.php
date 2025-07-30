<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $song = Song::factory()->createOne();

    $response = delete(route('api.song.destroy', ['song' => $song]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.song.destroy', ['song' => $song]));

    $response->assertForbidden();
});

test('trashed', function () {
    $song = Song::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.song.destroy', ['song' => $song]));

    $response->assertNotFound();
});

test('deleted', function () {
    $song = Song::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.song.destroy', ['song' => $song]));

    $response->assertOk();
    $this->assertSoftDeleted($song);
});
