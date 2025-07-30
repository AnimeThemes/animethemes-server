<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = SongResource::factory()->raw();

    $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = SongResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = SongResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Song::class),
            CrudPermission::CREATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.songresource.store', ['song' => $song, 'resource' => $resource] + $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(SongResource::class, 1);
});
