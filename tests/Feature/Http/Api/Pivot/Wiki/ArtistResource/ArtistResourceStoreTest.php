<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = ArtistResource::factory()->raw();

    $response = post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = ArtistResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $artist = Artist::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = ArtistResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Artist::class),
            CrudPermission::CREATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.artistresource.store', ['artist' => $artist, 'resource' => $resource] + $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(ArtistResource::class, 1);
});
