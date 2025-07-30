<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

    $response->assertForbidden();
});

test('not found', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(Song::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artist, 'song' => $song]));

    $response->assertNotFound();
});

test('deleted', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Artist::class),
            CrudPermission::DELETE->format(Song::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artistsong.destroy', ['artist' => $artistSong->artist, 'song' => $artistSong->song]));

    $response->assertOk();
    static::assertModelMissing($artistSong);
});
