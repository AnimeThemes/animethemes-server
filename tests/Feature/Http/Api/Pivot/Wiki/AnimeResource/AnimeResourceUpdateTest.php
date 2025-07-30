<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = AnimeResource::factory()->raw();

    $response = put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = AnimeResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $animeResource = AnimeResource::factory()
        ->for(Anime::factory())
        ->for(ExternalResource::factory(), AnimeResource::RELATION_RESOURCE)
        ->createOne();

    $parameters = AnimeResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::UPDATE->format(Anime::class),
            CrudPermission::UPDATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animeresource.update', ['anime' => $animeResource->anime, 'resource' => $animeResource->resource] + $parameters));

    $response->assertOk();
});
