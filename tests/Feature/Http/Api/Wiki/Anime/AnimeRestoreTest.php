<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $response = patch(route('api.anime.restore', ['anime' => $anime]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.anime.restore', ['anime' => $anime]));

    $response->assertForbidden();
});

test('trashed', function () {
    $anime = Anime::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.anime.restore', ['anime' => $anime]));

    $response->assertForbidden();
});

test('restored', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.anime.restore', ['anime' => $anime]));

    $response->assertOk();
    $this->assertNotSoftDeleted($anime);
});
