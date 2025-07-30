<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $song = Song::factory()->createOne();

    $parameters = Song::factory()->raw();

    $response = put(route('api.song.update', ['song' => $song] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->createOne();

    $parameters = Song::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.song.update', ['song' => $song] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $song = Song::factory()->trashed()->createOne();

    $parameters = Song::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.song.update', ['song' => $song] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $song = Song::factory()->createOne();

    $parameters = Song::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.song.update', ['song' => $song] + $parameters));

    $response->assertOk();
});
