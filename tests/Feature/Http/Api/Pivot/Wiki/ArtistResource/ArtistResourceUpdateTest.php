<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = ArtistResource::factory()->raw();

    $response = put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = ArtistResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $artistResource = ArtistResource::factory()
        ->for(Artist::factory())
        ->for(ExternalResource::factory(), ArtistResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = ArtistResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Artist::class),
            CrudPermission::UPDATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.artistresource.update', ['artist' => $artistResource->artist, 'resource' => $artistResource->resource] + $parameters));

    $response->assertOk();
});
