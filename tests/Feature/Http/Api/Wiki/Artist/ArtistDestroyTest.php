<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $artist = Artist::factory()->createOne();

    $response = delete(route('api.artist.destroy', ['artist' => $artist]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.artist.destroy', ['artist' => $artist]));

    $response->assertForbidden();
});

test('trashed', function () {
    $artist = Artist::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.artist.destroy', ['artist' => $artist]));

    $response->assertNotFound();
});

test('deleted', function () {
    $artist = Artist::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.artist.destroy', ['artist' => $artist]));

    $response->assertOk();
    $this->assertSoftDeleted($artist);
});
