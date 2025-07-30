<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

    $response->assertForbidden();
});

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animeresource.destroy', ['anime' => $anime, 'resource' => $resource]));

    $response->assertNotFound();
});

test('deleted', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animeresource.destroy', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource]));

    $response->assertOk();
    static::assertModelMissing($animeResource);
});
