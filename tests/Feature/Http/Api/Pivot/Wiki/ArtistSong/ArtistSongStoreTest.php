<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $parameters = ArtistSong::factory()->raw();

    $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $parameters = ArtistSong::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $artist = Artist::factory()->createOne();
    $song = Song::factory()->createOne();

    $parameters = ArtistSong::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Artist::class),
            CrudPermission::CREATE->format(Song::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artistsong.store', ['artist' => $artist, 'song' => $song] + $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(ArtistSong::class, 1);
});
