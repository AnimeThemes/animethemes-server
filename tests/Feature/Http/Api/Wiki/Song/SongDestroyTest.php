<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $song = Song::factory()->createOne();

    $response = $this->delete(route('api.song.destroy', ['song' => $song]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.song.destroy', ['song' => $song]));

    $response->assertForbidden();
});

test('trashed', function () {
    $song = Song::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.song.destroy', ['song' => $song]));

    $response->assertNotFound();
});

test('deleted', function () {
    $song = Song::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.song.destroy', ['song' => $song]));

    $response->assertOk();
    static::assertSoftDeleted($song);
});
