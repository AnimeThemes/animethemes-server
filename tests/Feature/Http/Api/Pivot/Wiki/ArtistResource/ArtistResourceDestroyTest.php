<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

    $response->assertForbidden();
});

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artist, 'resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistresource.destroy', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource]));

    $response->assertOk();
    static::assertModelMissing($artistResource);
});
