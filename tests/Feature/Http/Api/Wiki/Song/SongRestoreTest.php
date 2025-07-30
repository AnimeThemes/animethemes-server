<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $song = Song::factory()->trashed()->createOne();

    $response = patch(route('api.song.restore', ['song' => $song]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.song.restore', ['song' => $song]));

    $response->assertForbidden();
});

test('trashed', function () {
    $song = Song::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.song.restore', ['song' => $song]));

    $response->assertForbidden();
});

test('restored', function () {
    $song = Song::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.song.restore', ['song' => $song]));

    $response->assertOk();
    $this->assertNotSoftDeleted($song);
});
