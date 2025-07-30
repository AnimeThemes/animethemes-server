<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $response = patch(route('api.artist.restore', ['artist' => $artist]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.artist.restore', ['artist' => $artist]));

    $response->assertForbidden();
});

test('trashed', function () {
    $artist = Artist::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.artist.restore', ['artist' => $artist]));

    $response->assertForbidden();
});

test('restored', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.artist.restore', ['artist' => $artist]));

    $response->assertOk();
    $this->assertNotSoftDeleted($artist);
});
