<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = SongResource::factory()->raw();

    $response = put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = SongResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $songResource = SongResource::factory()
        ->for(Song::factory())
        ->for(ExternalResource::factory(), SongResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = SongResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Song::class),
            CrudPermission::UPDATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.songresource.update', ['song' => $songResource->song, 'resource' => $songResource->resource] + $parameters));

    $response->assertOk();
});
