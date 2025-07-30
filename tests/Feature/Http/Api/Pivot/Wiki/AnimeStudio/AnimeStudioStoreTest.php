<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $response = post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

    $response->assertForbidden();
});

test('create', function () {
    $anime = Anime::factory()->createOne();
    $studio = Studio::factory()->createOne();

    $user = User::factory()
        ->withPermissions(
            CrudPermission::CREATE->format(Anime::class),
            CrudPermission::CREATE->format(Studio::class)
        )
        ->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animestudio.store', ['anime' => $anime, 'studio' => $studio]));

    $response->assertCreated();
    $this->assertDatabaseCount(AnimeStudio::class, 1);
});
