<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('authorized', function () {
    $anime = Anime::factory()->createOne();

    $response = delete(route('api.anime.forceDelete', ['anime' => $anime]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.anime.forceDelete', ['anime' => $anime]));

    $response->assertForbidden();
});

test('deleted', function () {
    $anime = Anime::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.anime.forceDelete', ['anime' => $anime]));

    $response->assertOk();
    $this->assertModelMissing($anime);
});
