<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('trashed', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertNotFound();
});

test('deleted', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animetheme.destroy', ['animetheme' => $theme]));

    $response->assertOk();
    static::assertSoftDeleted($theme);
});
