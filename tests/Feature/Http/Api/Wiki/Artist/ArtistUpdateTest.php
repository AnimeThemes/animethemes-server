<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artist = Artist::factory()->createOne();

    $parameters = Artist::factory()->raw();

    $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();

    $parameters = Artist::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $parameters = Artist::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $artist = Artist::factory()->createOne();

    $parameters = Artist::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.artist.update', ['artist' => $artist] + $parameters));

    $response->assertOk();
});
