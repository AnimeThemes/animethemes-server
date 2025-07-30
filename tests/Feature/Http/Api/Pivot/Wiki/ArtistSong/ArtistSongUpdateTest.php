<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\ArtistSong;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $parameters = ArtistSong::factory()->raw();

    $response = put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $parameters = ArtistSong::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $artistSong = ArtistSong::factory()
        ->for(Artist::factory())
        ->for(Song::factory())
        ->createOne();

    $parameters = ArtistSong::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Artist::class),
            CrudPermission::UPDATE->format(Song::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.artistsong.update', ['artist' => $artistSong->artist, 'song' => $artistSong->song] + $parameters));

    $response->assertOk();
});
