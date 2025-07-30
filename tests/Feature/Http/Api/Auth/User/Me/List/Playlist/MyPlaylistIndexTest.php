<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    $response = get(route('api.me.playlist.index'));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.me.playlist.index'));

    $response->assertForbidden();
});

test('only sees owned playlists', function () {
    Playlist::factory()
        ->for(User::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    Playlist::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(Playlist::class))->createOne();

    $playlistCount = fake()->randomDigitNotNull();

    $playlists = Playlist::factory()
        ->for($user)
        ->count($playlistCount)
        ->create();

    Sanctum::actingAs($user);

    $response = get(route('api.me.playlist.index'));

    $response->assertJsonCount($playlistCount, PlaylistCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new PlaylistCollection($playlists, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
