<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = AnimeResource::factory()->raw();

    $response = post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = AnimeResource::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('create', function () {
    $anime = Anime::factory()->createOne();
    $resource = ExternalResource::factory()->createOne();

    $parameters = AnimeResource::factory()->raw();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Anime::class),
            CrudPermission::CREATE->format(ExternalResource::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animeresource.store', ['anime' => $anime, 'resource' => $resource] + $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(AnimeResource::class, 1);
});
