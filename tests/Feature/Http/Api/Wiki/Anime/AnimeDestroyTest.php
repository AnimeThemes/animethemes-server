<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $anime = Anime::factory()->createOne();

    $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

    $response->assertForbidden();
});

test('trashed', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

    $response->assertNotFound();
});

test('deleted', function () {
    $anime = Anime::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.anime.destroy', ['anime' => $anime]));

    $response->assertOk();
    static::assertSoftDeleted($anime);
});
