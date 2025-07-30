<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

    $response->assertForbidden();
});

test('not found', function () {
    $song = Song::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Song::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.songresource.destroy', ['song' => $song, 'resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Song::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.songresource.destroy', ['song' => $songResource->song, 'resource' => $songResource->resource]));

    $response->assertOk();
    static::assertModelMissing($songResource);
});
