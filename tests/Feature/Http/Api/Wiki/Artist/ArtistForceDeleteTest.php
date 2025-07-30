<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artist = Artist::factory()->createOne();

    $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

    $response->assertForbidden();
});

test('deleted', function () {
    $artist = Artist::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.artist.forceDelete', ['artist' => $artist]));

    $response->assertOk();
    static::assertModelMissing($artist);
});
