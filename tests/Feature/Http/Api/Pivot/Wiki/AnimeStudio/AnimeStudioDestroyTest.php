<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $response = delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

    $response->assertForbidden();
});

test('not found', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Studio::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animestudio.destroy', ['anime' => $anime, 'studio' => $studio]));

    $response->assertNotFound();
});

test('deleted', function () {
    $animeStudio = AnimeStudio::factory()
        ->for(Anime::factory())
        ->for(Studio::factory())
        ->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::DELETE->format(Anime::class),
            CrudPermission::DELETE->format(Studio::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.animestudio.destroy', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

    $response->assertOk();
    $this->assertModelMissing($animeStudio);
});
