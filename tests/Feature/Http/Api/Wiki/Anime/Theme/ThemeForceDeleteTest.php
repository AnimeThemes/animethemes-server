<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $response = $this->delete(route('api.animetheme.forceDelete', ['animetheme' => $theme]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animetheme.forceDelete', ['animetheme' => $theme]));

    $response->assertForbidden();
});

test('deleted', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.animetheme.forceDelete', ['animetheme' => $theme]));

    $response->assertOk();
    static::assertModelMissing($theme);
});
